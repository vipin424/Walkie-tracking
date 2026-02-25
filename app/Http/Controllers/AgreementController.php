<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderAgreement;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\SignedAgreementMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Mail\AgreementLinkMail;
use Illuminate\Support\Facades\Log;

class AgreementController extends Controller
{
    public function show($code)
    {
        $agreement = OrderAgreement::where('agreement_code', $code)->firstOrFail();

        abort_if(now()->greaterThan($agreement->expires_at), 403);

        return view('agreement.sign', compact('agreement'));
    }

    public function submit(Request $request, $code)
    {
        $agreement = OrderAgreement::where('agreement_code', $code)->firstOrFail();

        abort_if($agreement->status === 'signed', 403);
        abort_if(now()->greaterThan($agreement->expires_at), 403);

        $request->validate([
            'signature' => 'required|string',
        ]);

        /** 🔹 SAVE SIGNATURE IMAGE */
        $signature = str_replace('data:image/png;base64,', '', $request->signature);
        $signature = base64_decode($signature);

        $signaturePath = 'signatures/' . $agreement->agreement_code . '.png';
        Storage::disk('public')->put($signaturePath, $signature);

        /** 🔹 UPDATE AGREEMENT */
        $agreement->update([
            'signature_image' => $signaturePath,
            'signed_at' => now(),
            'status' => 'signed',
        ]);

        /** 🔹 GENERATE SIGNED AGREEMENT PDF */
        $pdf = Pdf::loadView('agreement.pdf', [
            'agreement' => $agreement->fresh('order'),
        ])->setPaper('a4');

        $pdfPath = 'agreements/signed_' . $agreement->agreement_code . '.pdf';
        Storage::disk('public')->put($pdfPath, $pdf->output());

        $agreement->update([
            'signed_pdf' => $pdfPath,
        ]);

        /** 🔹 EMAIL TO CLIENT WITH ATTACHMENTs */
        $agreement = $agreement->fresh();

        if ($agreement->order->client_email) {
            Mail::to($agreement->order->client_email)
                ->cc(config('mail.admin_email'))
                ->send(new SignedAgreementMail($agreement));
        }


        return redirect()
            ->back()
            ->with('agreement_signed', true);
    }

    /**
     * Send Agreement link via Email
     */

    public function sendEmail(Request $request, Order $order)
    {
        $agreement = $order->agreement;

        abort_if(!$agreement || !$agreement->signed_url, 403);

        // ✅ Validation (modal se aayega)
        $request->validate([
            'to_email'  => 'required|email',
            'cc_emails' => 'nullable|string',
            'message'   => 'nullable|string',
        ]);

        // ✅ CC emails parse
        $cc = [];

        if ($request->filled('cc_emails')) {
            $cc = collect(explode(',', $request->cc_emails))
                ->map(fn ($email) => trim($email))
                ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
                ->values()
                ->toArray();
        }

        // ✅ Send mail (existing mailable reused)
        Mail::to($request->to_email)
            ->cc($cc)
            ->send(
                new AgreementLinkMail(
                    $order,
                    $agreement->signed_url
                    //$request->message // optional message support
                )
            );

        // ✅ Update sent timestamp
        $agreement->update([
            'sent_at' => now(),
        ]);

        return back()->with('success', 'Agreement link sent via email.');
    }


    /**
     * Send Agreement link via WhatsApp
     */

    public function sendWhatsapp(Order $order)
    {
        abort_if(!$order->agreement, 404);

        $phone = preg_replace('/\D+/', '', $order->client_phone);
        if (strlen($phone) <= 10) {
            $phone = '91' . $phone;
        }

        $message = "Hello {$order->client_name},\n"
                 . "Please sign the agreement for Order {$order->order_code}.\n\n"
                 . "Link (valid 48 hrs):\n"
                 . $order->agreement->signed_url;

        $waLink = 'https://wa.me/' . $phone . '?text=' . urlencode($message);

        return redirect($waLink);
    }

}
