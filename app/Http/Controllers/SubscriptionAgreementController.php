<?php

namespace App\Http\Controllers;

use App\Models\MonthlySubscription;
use App\Models\SubscriptionAgreement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Mail\SubscriptionAgreementLinkMail;
use App\Mail\SignedSubscriptionAgreementMail;
use Illuminate\Support\Facades\Log;

class SubscriptionAgreementController extends Controller
{
    /**
     * Generate (or regenerate) agreement for a subscription.
     * Admin provides start_date + end_date via modal.
     */
    public function generate(Request $request, MonthlySubscription $subscription)
    {
        $request->validate([
            'agreement_start_date' => 'required|date',
            'agreement_end_date'   => 'required|date|after:agreement_start_date',
            'security_deposit'     => 'nullable|numeric|min:0',
        ]);

        $existing = $subscription->agreement;

        // Build signed URL (client-facing, no-auth sign page)
        $code = $existing ? $existing->agreement_code : SubscriptionAgreement::generateCode();

        $signedUrl = URL::to('/subscription-agreement/sign/' . $code);

        if ($existing) {
            // Regenerate: update dates, deposit & reset to pending
            $existing->update([
                'agreement_start_date' => $request->agreement_start_date,
                'agreement_end_date'   => $request->agreement_end_date,
                'security_deposit'     => $request->security_deposit,
                'signed_url'           => $signedUrl,
                'expires_at'           => now()->addDays(90),
                'status'               => 'pending',
                'signed_at'            => null,
                'signature_image'      => null,
                'signed_pdf'           => null,
                'sent_at'              => null,
            ]);
            $agreement = $existing;
        } else {
            $agreement = SubscriptionAgreement::create([
                'subscription_id'      => $subscription->id,
                'agreement_code'       => $code,
                'agreement_start_date' => $request->agreement_start_date,
                'agreement_end_date'   => $request->agreement_end_date,
                'security_deposit'     => $request->security_deposit,
                'signed_url'           => $signedUrl,
                'expires_at'           => now()->addDays(90),
                'status'               => 'pending',
            ]);
        }

        return back()->with('success', 'Agreement generated successfully. You can now send the signing link to the client.');
    }

    /**
     * Send agreement signing link via Email.
     */
    public function sendEmail(Request $request, MonthlySubscription $subscription)
    {
        $agreement = $subscription->agreement;
        abort_if(!$agreement, 404, 'Agreement not generated yet.');

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
            ->send(new SubscriptionAgreementLinkMail($subscription, $agreement->signed_url));

        $agreement->update(['sent_at' => now()]);

        return back()->with('success', 'Agreement link sent via email successfully.');
    }

    /**
     * Send agreement signing link via WhatsApp.
     */
    public function sendWhatsapp(MonthlySubscription $subscription)
    {
        $agreement = $subscription->agreement;
        abort_if(!$agreement, 404);

        $phone = preg_replace('/\D+/', '', $subscription->client_phone);
        if (strlen($phone) <= 10) {
            $phone = '91' . $phone;
        }

        $message = "Hello *{$subscription->client_name}*,\n\n"
            . "Please sign the Monthly Equipment Rental Agreement for Subscription *{$subscription->subscription_code}*.\n\n"
            . "📅 *Agreement Period:* {$agreement->agreement_start_date->format('d M Y')} to {$agreement->agreement_end_date->format('d M Y')}\n"
            . "💰 *Monthly Amount:* ₹" . number_format($subscription->monthly_amount, 2) . "\n\n"
            . "🔗 *Sign here (link valid 90 days):*\n{$agreement->signed_url}\n\n"
            . "– *Crewrent Enterprises*";

        $waLink = 'https://wa.me/' . $phone . '?text=' . urlencode($message);

        $agreement->update(['sent_at' => now()]);

        return redirect($waLink);
    }

    /**
     * Client-facing sign page (no auth).
     */
    public function show($code)
    {
        $agreement = SubscriptionAgreement::with('subscription')
            ->where('agreement_code', $code)
            ->firstOrFail();

        abort_if(now()->greaterThan($agreement->expires_at), 403, 'This agreement link has expired.');

        return view('subscription-agreement.sign', compact('agreement'));
    }

    /**
     * Client submits signature.
     */
    public function submit(Request $request, $code)
    {
        // Extend PHP time limit — SMTP can be slow on shared hosting
        @set_time_limit(180);

        $agreement = SubscriptionAgreement::with('subscription')
            ->where('agreement_code', $code)
            ->firstOrFail();

        abort_if($agreement->status === 'signed', 403, 'This agreement has already been signed.');
        abort_if(now()->greaterThan($agreement->expires_at), 403, 'This agreement link has expired.');

        $request->validate([
            'signature' => 'required|string',
        ]);

        // ✅ STEP 1: Save signature image
        $signatureData = str_replace('data:image/png;base64,', '', $request->signature);
        $signatureData = base64_decode($signatureData);
        $signaturePath = 'sub-signatures/' . $agreement->agreement_code . '.png';
        Storage::disk('public')->put($signaturePath, $signatureData);

        // ✅ STEP 2: Mark agreement as signed in DB immediately
        $agreement->update([
            'signature_image' => $signaturePath,
            'signed_at'       => now(),
            'status'          => 'signed',
        ]);

        // ✅ STEP 3: Generate signed PDF
        try {
            $agreement->load('subscription');
            $pdf = Pdf::loadView('subscription-agreement.pdf', [
                'agreement' => $agreement,
            ])->setPaper('a4');

            $pdfPath = 'sub-agreements/signed_' . $agreement->agreement_code . '.pdf';
            Storage::disk('public')->makeDirectory('sub-agreements');
            Storage::disk('public')->put($pdfPath, $pdf->output());
            $agreement->update(['signed_pdf' => $pdfPath]);
        } catch (\Throwable $e) {
            Log::error('Subscription agreement PDF generation failed: ' . $e->getMessage());
        }

        // ✅ STEP 4: Send email (non-fatal — if SMTP fails, success page still shows)
        $subscription = $agreement->subscription;
        if ($subscription && $subscription->client_email) {
            try {
                $adminEmail = config('mail.admin_email');
                $mailInstance = Mail::to($subscription->client_email);
                if ($adminEmail) {
                    $mailInstance->cc([$adminEmail]);
                }
                $mailInstance->send(new SignedSubscriptionAgreementMail($agreement->fresh(['subscription'])));
            } catch (\Throwable $e) {
                // Email failed — signature is already saved, so client experience is intact
                Log::error('Signed subscription agreement email failed: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('agreement_signed', true);
    }
}
