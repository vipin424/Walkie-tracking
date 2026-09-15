<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AddendumAgreementLinkMail extends Mailable
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

        return $this
            ->subject('New Items Added — Please Sign Addendum | ' . $subscription->subscription_code)
            ->view('emails.addendum-agreement-link');
    }
}
