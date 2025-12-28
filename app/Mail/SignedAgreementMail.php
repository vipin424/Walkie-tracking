<?php
namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class SignedAgreementMail extends Mailable
{
    public $agreement;

    public function __construct($agreement)
    {
        $this->agreement = $agreement;
    }

    public function build()
    {
        $mail = $this->subject(
            'Signed Agreement - '.$this->agreement->order->order_code
        )->view('emails.signed-agreement');

        // ✅ Attach only if PDF exists
        if ($this->agreement->signed_pdf &&
            Storage::disk('public')->exists($this->agreement->signed_pdf)
        ) {
            $mail->attachFromStorageDisk(
                'public',
                $this->agreement->signed_pdf,
                'Signed-Agreement-'.$this->agreement->agreement_code.'.pdf'
            );
        }

        return $mail;
    }

}
