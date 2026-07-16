<?php

namespace App\Mail;

use App\Models\MonthlySubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionAgreementLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public MonthlySubscription $subscription;
    public string $link;

    public function __construct(MonthlySubscription $subscription, string $link)
    {
        $this->subscription = $subscription;
        $this->link         = $link;
    }

    public function build()
    {
        return $this
            ->subject('Monthly Rental Agreement Signing Required | ' . $this->subscription->subscription_code)
            ->view('emails.subscription-agreement-link');
    }
}
