<?php

namespace App\Mail;

use App\Models\DamageReport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DamageInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public DamageReport $report,
        public string $downloadUrl,
        public ?string $customMessage = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Damage / Replacement Invoice – ' . $this->report->report_code . ' | Crewrent Enterprises',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.damage-invoice',
        );
    }
}
