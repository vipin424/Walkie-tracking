<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class SignedAddendumAgreementMail extends Mailable
{
    use Queueable, SerializesModels;

    public $addendum;

    public function __construct($addendum)
    {
        $this->addendum = $addendum;
    }

    public function build()
    {
        $subscription = $this->addendum->subscription;

        $mail = $this
            ->subject('Signed Addendum Agreement — ' . $subscription->subscription_code)
            ->view('emails.signed-addendum-agreement');

        // Attach signed PDF if available
        if ($this->addendum->signed_pdf &&
            Storage::disk('public')->exists($this->addendum->signed_pdf)
        ) {
            $mail->attachFromStorageDisk(
                'public',
                $this->addendum->signed_pdf,
                'Signed-Addendum-' . $this->addendum->addendum_code . '.pdf'
            );
        }

        return $mail;
    }
}
