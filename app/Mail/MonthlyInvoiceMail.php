<?php

namespace App\Mail;

use App\Models\MonthlyInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MonthlyInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $downloadUrl;
    public $customMessage;

    public function __construct(MonthlyInvoice $invoice, $downloadUrl, $customMessage = null)
    {
        $this->invoice = $invoice;
        $this->downloadUrl = $downloadUrl;
        $this->customMessage = $customMessage;
    }

    public function build()
    {
        $subject = "Monthly Invoice {$this->invoice->invoice_code} - Crewrent Enterprises";
        
        return $this->subject($subject)
                    ->view('emails.monthly-invoice')
                    ->with([
                        'invoice' => $this->invoice,
                        'downloadUrl' => $this->downloadUrl,
                        'customMessage' => $this->customMessage,
                    ]);
    }
}
