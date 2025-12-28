<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AgreementLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public string $link;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, string $link)
    {
        $this->order = $order;
        $this->link  = $link;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this
            ->subject('Agreement Signing Required | Order ' . $this->order->order_code)
            ->view('emails.agreement-link');
    }
}
