<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderMailable extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public ?string $customMessage;
    public $downloadUrl;

    public function __construct(Order $order, ?string $customMessage = null, $downloadUrl = null)
    {
        $this->order = $order;
        $this->customMessage = $customMessage;
        $this->downloadUrl = $downloadUrl;      
    }

    public function build()
    {
        return $this
            ->subject('Order Confirmed - ' . $this->order->order_code)
            ->view('emails.order')
            ->with([
                'order' => $this->order,
                'messageText' => $this->customMessage,
            ])
            ->attach(
                storage_path(
                    'app/' . str_replace('storage/', 'public/', $this->order->pdf_path)
                ),
                [
                    'as'   => $this->order->order_code . '.pdf',
                    'mime' => 'application/pdf',
                ]
            );
    }
}
