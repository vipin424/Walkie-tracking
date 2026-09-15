<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AgreementExpiryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $type;       // 'main' or 'addendum'
    public $agreement;  // The model instance
    public $clientName;
    public $subscriptionCode;
    public $endDate;

    public function __construct($type, $agreement)
    {
        $this->type = $type;
        $this->agreement = $agreement;
        $this->clientName = $agreement->subscription->client_name;
        $this->subscriptionCode = $agreement->subscription->subscription_code;
        $this->endDate = $agreement->agreement_end_date->format('d M Y');
    }

    public function build()
    {
        $docType = $this->type === 'main' ? 'Rental Agreement' : 'Addendum Agreement';
        $docCode = $this->type === 'main' ? $this->agreement->agreement_code : $this->agreement->addendum_code;

        return $this
            ->subject("Action Required: Your $docType is Expiring Soon - $this->subscriptionCode")
            ->view('emails.agreement-expiry', [
                'docType' => $docType,
                'docCode' => $docCode,
            ]);
    }
}
