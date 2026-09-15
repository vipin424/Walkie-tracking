<?php

namespace App\Http\Controllers;

use App\Models\MonthlySubscription;
use App\Models\SubscriptionAddendum;
use App\Services\SubscriptionBillingService;
use App\Mail\SignedAddendumAgreementMail;
use App\Mail\AddendumAgreementLinkMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SubscriptionAddendumController extends Controller
{
    protected SubscriptionBillingService $billingService;

    public function __construct(SubscriptionBillingService $billingService)
    {
        $this->billingService = $billingService;
    }

    /**
     * Auto-generate an addendum when mid-cycle items are detected on update.
     * Called internally from MonthlySubscriptionController after update().
     */
    public static function autoGenerate(MonthlySubscription $subscription, array $newItems, $endDate = null): ?SubscriptionAddendum
    {
        $billingService  = app(SubscriptionBillingService::class);
        $billingDay      = $subscription->billing_day_of_month;
        $today           = Carbon::today();
        $nextBillingDate = $billingService->getNextBillingDate($billingDay);
        $daysRemaining   = max(1, (int) $today->diffInDays($nextBillingDate));

        // Compute pro-rated amounts for new items
        $newItemsWithAmounts = [];
        $proRatedTotal = 0;
        foreach ($newItems as $item) {
            $rate     = (float) ($item['rate'] ?? 0);
            $qty      = (int)   ($item['quantity'] ?? 1);
            $proAmt   = round($rate * $qty * ($daysRemaining / 30), 2);
            $newItemsWithAmounts[] = array_merge($item, [
                'pro_rated_amount' => $proAmt,
                'pro_rated_days'   => $daysRemaining,
                'pro_rated_until'  => $nextBillingDate->toDateString(),
            ]);
            $proRatedTotal += $proAmt;
        }

        $code      = SubscriptionAddendum::generateCode();
        $signedUrl = URL::to('/subscription-addendum/sign/' . $code);

        return SubscriptionAddendum::create([
            'subscription_id'     => $subscription->id,
            'addendum_code'       => $code,
            'effective_date'      => $today->toDateString(),
            'agreement_end_date'  => $endDate,
            'new_items_json'      => $newItemsWithAmounts,
            'pro_rated_amount'    => round($proRatedTotal, 2),
            'new_monthly_amount'  => $subscription->monthly_amount,
            'billing_day_of_month'=> $billingDay,
            'pro_rated_days'      => $daysRemaining,
            'pro_rated_until'     => $nextBillingDate->toDateString(),
            'signed_url'          => $signedUrl,
            'expires_at'          => now()->addDays(90),
            'status'              => 'pending',
        ]);
    }

    /**
     * Send addendum signing link via Email.
     */
    public function sendEmail(Request $request, SubscriptionAddendum $addendum)
    {
        $request->validate([
            'to_email'  => 'required|email',
            'cc_emails' => 'nullable|string',
        ]);

        $cc = [];
        if ($request->filled('cc_emails')) {
            $cc = collect(explode(',', $request->cc_emails))
                ->map(fn($e) => trim($e))
                ->filter(fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL))
                ->values()
                ->toArray();
        }

        Mail::to($request->to_email)
            ->cc($cc)
            ->send(new AddendumAgreementLinkMail($addendum));

        $addendum->update(['sent_at' => now()]);

        return back()->with('success', 'Addendum signing link sent via email successfully.');
    }

    /**
     * Send addendum signing link via WhatsApp.
     */
    public function sendWhatsapp(SubscriptionAddendum $addendum)
    {
        $subscription = $addendum->subscription;
        $phone        = preg_replace('/\D+/', '', $subscription->client_phone);
        if (strlen($phone) <= 10) $phone = '91' . $phone;

        $newItemNames = collect($addendum->new_items_json)->pluck('name')->implode(', ');

        $message = "Hello *{$subscription->client_name}*,\n\n"
            . "An addendum has been added to your subscription *{$subscription->subscription_code}*.\n\n"
            . "📦 *New Items Added:* {$newItemNames}\n"
            . "📅 *Effective Date:* {$addendum->effective_date->format('d M Y')}\n"
            . "💰 *Pro-Rated Charge (this cycle):* ₹" . number_format($addendum->pro_rated_amount, 2) . "\n"
            . "💰 *New Monthly Amount:* ₹" . number_format($addendum->new_monthly_amount, 2) . "\n\n"
            . "🔗 *Please sign the addendum here (valid 90 days):*\n{$addendum->signed_url}\n\n"
            . "– *Crewrent Enterprises*";

        $addendum->update(['sent_at' => now()]);

        return redirect('https://wa.me/' . $phone . '?text=' . urlencode($message));
    }

    /**
     * Client-facing addendum sign page (no auth).
     */
    public function show($code)
    {
        $addendum = SubscriptionAddendum::with('subscription')
            ->where('addendum_code', $code)
            ->firstOrFail();

        abort_if(now()->greaterThan($addendum->expires_at), 403, 'This addendum link has expired.');

        return view('subscription-addendum.sign', compact('addendum'));
    }

    /**
     * Client submits signature on addendum.
     */
    public function submit(Request $request, $code)
    {
        @set_time_limit(180);

        $addendum = SubscriptionAddendum::with('subscription')
            ->where('addendum_code', $code)
            ->firstOrFail();

        abort_if($addendum->status === 'signed', 403, 'This addendum has already been signed.');
        abort_if(now()->greaterThan($addendum->expires_at), 403, 'This addendum link has expired.');

        $request->validate(['signature' => 'required|string']);

        // STEP 1: Save signature image
        $signatureData = base64_decode(str_replace('data:image/png;base64,', '', $request->signature));
        $signaturePath = 'sub-addendum-signatures/' . $addendum->addendum_code . '.png';
        Storage::disk('public')->makeDirectory('sub-addendum-signatures');
        Storage::disk('public')->put($signaturePath, $signatureData);

        // STEP 2: Mark as signed
        $addendum->update([
            'signature_image' => $signaturePath,
            'signed_at'       => now(),
            'status'          => 'signed',
        ]);

        // STEP 3: Generate signed PDF
        try {
            $addendum->load('subscription');
            $pdf = Pdf::loadView('subscription-addendum.pdf', ['addendum' => $addendum])->setPaper('a4');

            $pdfPath = 'sub-addendums/signed_' . $addendum->addendum_code . '.pdf';
            Storage::disk('public')->makeDirectory('sub-addendums');
            Storage::disk('public')->put($pdfPath, $pdf->output());
            $addendum->update(['signed_pdf' => $pdfPath]);
        } catch (\Throwable $e) {
            Log::error('Addendum PDF generation failed: ' . $e->getMessage());
        }

        // STEP 4: Send signed PDF via email to client + admin (CC)
        $subscription = $addendum->subscription;
        if ($subscription && $subscription->client_email) {
            try {
                $adminEmail   = config('mail.admin_email') ?? env('ADMIN_EMAIL', 'info@crewrent.in');
                $mailInstance = Mail::to($subscription->client_email);
                if ($adminEmail) {
                    $mailInstance->cc([$adminEmail]);
                }
                $mailInstance->send(new SignedAddendumAgreementMail($addendum->fresh(['subscription'])));
            } catch (\Throwable $e) {
                Log::error('Signed addendum email failed: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('addendum_signed', true);
    }
}
