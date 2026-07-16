<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class SignedSubscriptionAgreementMail extends Mailable
{
    use Queueable, SerializesModels;

    public $agreement;

    public function __construct($agreement)
    {
        $this->agreement = $agreement;
    }

    public function build()
    {
        $mail = $this
            ->subject('Signed Monthly Rental Agreement - ' . $this->agreement->subscription->subscription_code)
            ->view('emails.signed-subscription-agreement');

        // Attach PDF if exists
        if ($this->agreement->signed_pdf &&
            Storage::disk('public')->exists($this->agreement->signed_pdf)
        ) {
            $mail->attachFromStorageDisk(
                'public',
                $this->agreement->signed_pdf,
                'Signed-Agreement-' . $this->agreement->agreement_code . '.pdf'
            );
        }

        return $mail;
    }
}
