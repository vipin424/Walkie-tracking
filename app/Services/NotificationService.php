<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderLog;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotificationService
{
    protected $pdfService;

    public function __construct(PdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * Generates a pre-filled WhatsApp link for the order and logs it.
     */
    public function generateWhatsAppLink(Order $order): string
    {
        // Ensure PDF exists
        if (!$order->pdf_path || !\Storage::disk('public')->exists(str_replace('storage/', '', $order->pdf_path))) {
            $this->pdfService->generateOrderPdf($order);
            $order->refresh();
        }

        // Generate signed download URL
        $hash = substr(md5($order->id . config('app.key')), 0, 8);
        $url = URL::temporarySignedRoute(
            'orders.download',
            now()->addDays(7),
            ['hash' => $hash, 'ref' => $order->id]
        );

        // WhatsApp message
        $messageText =
            "Hello *{$order->client_name}*,\n\n" .
            "Your order *{$order->order_code}* is confirmed.\n\n" .
            "📅 *Event Date:* " .
            Carbon::parse($order->event_from)->format('d M Y') .
            " to " .
            Carbon::parse($order->event_to)->format('d M Y') . "\n\n" .
            "💰 *Payment Details:*\n" .
            "Total Amount: ₹" . number_format($order->total_amount, 2) . "\n" .
            "Advance Paid: ₹" . number_format($order->advance_paid, 2) . "\n" .
            "Remaining Balance: ₹" . number_format($order->balance_amount, 2) . "\n\n" .
            "Download Order PDF:\n{$url}\n\n" .
            "This link is valid for 7 days.\n\n" .
            "– *Crewrent Enterprises*";

        // Normalize phone number
        $phone = preg_replace('/\D+/', '', $order->client_phone);
        if (strlen($phone) <= 10) {
            $phone = '91' . $phone;
        }

        // Log
        OrderLog::create([
            'order_id' => $order->id,
            'user_id' => Auth::id() ?? 1,
            'action' => 'auto_sent_whatsapp',
            'meta' => json_encode(['to' => $phone, 'link' => $url]),
        ]);

        return 'https://wa.me/' . $phone . '?text=' . rawurlencode($messageText);
    }
}
