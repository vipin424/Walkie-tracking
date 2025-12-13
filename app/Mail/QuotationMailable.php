<?php

namespace App\Mail;

use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QuotationMailable extends Mailable
{
    use Queueable, SerializesModels;

    public Quotation $quotation;
    public ?string $customMessage;

    public function __construct(Quotation $quotation, ?string $customMessage = null)
    {
        $this->quotation = $quotation;
        $this->customMessage = $customMessage;
    }

    public function build()
    {
        return $this
            ->subject('Quotation - ' . $this->quotation->code)
            ->view('emails.quotation')
            ->with([
                'quotation' => $this->quotation,
                'messageText' => $this->customMessage,
            ])
            ->attach(
                storage_path(
                    'app/' . str_replace('storage/', 'public/', $this->quotation->pdf_path)
                ),
                [
                    'as'   => $this->quotation->code . '.pdf',
                    'mime' => 'application/pdf',
                ]
            );
    }
}
