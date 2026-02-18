<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Dispatch;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\PaymentReminder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;  
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $payments = Payment::with('dispatch.client')
            ->when($status, fn($q)=>$q->where('payment_status',$status))
            ->orderByDesc('id')->paginate(20);

        return view('payments.index', compact('payments','status'));
    }

    public function storeOrUpdate(Request $request, Dispatch $dispatch)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:Paid,Unpaid,Advance Received',
            'advance_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string|max:500',
        ]);

        $payment = $dispatch->payment;

        // ---------------------------------------------
        // 🟡 Calculate remaining amount = total - advance
        // ---------------------------------------------
        $total = $validated['total_amount'] ?? $payment->total_amount;
        $advance = $validated['advance_amount'] ?? $payment->advance_amount;

        $finalTotal = max($total - $advance, 0);   // no negative totals

        // ---------------------------------------------
        // 🟢 Update with adjusted final amount
        // ---------------------------------------------
        $payment->update([
            'payment_status' => $validated['payment_status'],
            'advance_amount' => $advance,
            'total_amount'   => $finalTotal,
            'remarks'        => $validated['remarks'] ?? $payment->remarks,
        ]);

        // ---------------------------------------------
        // 🟣 Auto-generate invoice if returned
        // ---------------------------------------------
        if ($dispatch && $dispatch->status === Dispatch::STATUS_RETURNED) {
            $dispatch->generateInvoicePDF();
        }

        return back()->with('success','Payment updated');
    }

    /**
     * Send payment reminder via WhatsApp and/or Email
     */
    public function sendReminder(Request $request, Order $order)
    {
        $request->validate([
            'channel' => 'required|in:whatsapp,email,both',
            'message' => 'nullable|string'
        ]);

        $channel = $request->channel;
        $success = true;
        $whatsappLink = null;

        // Generate message
        $message = $request->message ?? $this->getDefaultReminderMessage($order);

        // Send WhatsApp reminder (via Web link)
        if (in_array($channel, ['whatsapp', 'both'])) {
            $whatsappLink = $this->getWhatsAppLink($order, $message);
        }

        // Send Email reminder
        if (in_array($channel, ['email', 'both'])) {
            $emailSuccess = $this->sendEmailReminder($order, $message);
            if (!$emailSuccess) {
                $success = false;
            }
        }

        // Log reminder
        PaymentReminder::create([
            'order_id' => $order->id,
            'channel' => $channel,
            'message' => $message,
            'sent_at' => now(),
            'sent_by' => auth()->user()->name ?? 'Admin'
        ]);

        return response()->json([
            'success' => $success,
            'message' => 'Reminder prepared successfully!',
            'whatsapp_link' => $whatsappLink
        ]);
    }

    /**
     * Record payment transaction
     */
    public function recordPayment(Request $request, Order $order)
    {
       // dd($request->all());
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:gpay,paytm,phonepe,cash,bank_transfer,upi,other',
            'transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'paid_at' => 'nullable|date'
        ]);

        // Validate amount doesn't exceed pending
        if ($request->amount > $order->final_payable) {
            return response()->json([
                'success' => false,
                'message' => 'Payment amount cannot exceed pending amount of ₹' . number_format($order->final_payable, 2)
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Create payment transaction
            $transaction = PaymentTransaction::create([
                'order_id' => $order->id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'transaction_id' => $request->transaction_id,
                'notes' => $request->notes,
                'paid_at' => $request->paid_at ?? now(),
                'recorded_by' => auth()->user()->name ?? 'Admin'
            ]);

            // Calculate total paid
            $totalPaid = PaymentTransaction::where('order_id', $order->id)->sum('amount');
            // Update order
            $remainingAmount = max(0, $order->final_payable - $totalPaid);

            
            if ($remainingAmount == 0) {
                $order->payment_status = 'paid';
            } elseif ($totalPaid > 0) {
                $order->payment_status = 'partial';
            }
            
            $order->final_payable = $remainingAmount;
            $order->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully!',
                'data' => [
                    'total_paid' => $totalPaid,
                    'remaining' => $remainingAmount,
                    'payment_status' => $order->payment_status
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment recording failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to record payment. Please try again.'
            ], 500);
        }
    }

    /**
     * Get payment history for an order
     */
    public function getPaymentHistory(Order $order)
    {
        $transactions = PaymentTransaction::where('order_id', $order->id)
            ->orderBy('paid_at', 'desc')
            ->get()
            ->map(function($t) {
                return [
                    'id' => $t->id,
                    'amount' => '₹' . number_format($t->amount, 2),
                    'method' => ucwords(str_replace('_', ' ', $t->payment_method)),
                    'transaction_id' => $t->transaction_id,
                    'date' => $t->paid_at->format('d M Y, h:i A'),
                    'recorded_by' => $t->recorded_by,
                    'notes' => $t->notes
                ];
            });

        $reminders = PaymentReminder::where('order_id', $order->id)
            ->orderBy('sent_at', 'desc')
            ->get()
            ->map(function($r) {
                return [
                    'id' => $r->id,
                    'channel' => ucfirst($r->channel),
                    'date' => $r->sent_at->format('d M Y, h:i A'),
                    'sent_by' => $r->sent_by,
                    'message' => $r->message
                ];
            });

        return response()->json([
            'order_code' => $order->order_code,
            'client_name' => $order->client_name,
            'total_amount' => $order->total_amount,
            'total_paid' => $transactions->sum('amount'),
            'pending_amount' => $order->final_payable,
            'transactions' => $transactions,
            'reminders' => $reminders
        ]);
    }

    /**
     * Get WhatsApp Web link
     */
    private function getWhatsAppLink(Order $order, string $message)
    {
        $phone = $this->formatPhoneNumber($order->client_phone);
        $encodedMessage = urlencode($message);
        
        return "https://wa.me/{$phone}?text={$encodedMessage}";
    }

    /**
     * Send Email reminder
     */
    private function sendEmailReminder(Order $order, string $message)
    {
        try {
            if (!$order->client_email) {
                Log::warning("Order {$order->order_code} has no email address");
                return false;
            }

            Mail::send([], [], function ($mail) use ($order, $message) {
                $mail->to($order->client_email)
                    ->subject('Crewrent Enterprises - Payment Reminder - Order ' . $order->order_code)
                    ->html($this->getEmailTemplate($order, $message));
            });

            return true;

        } catch (\Exception $e) {
            Log::error('Email reminder failed for order ' . $order->order_code . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get default reminder message
     */
    private function getDefaultReminderMessage(Order $order)
    {
        $eventFrom = \Carbon\Carbon::parse($order->event_from)->format('d M Y');
        $eventTo = \Carbon\Carbon::parse($order->event_to)->format('d M Y');
        $eventTime = $order->event_time ? \Carbon\Carbon::parse($order->event_time)->format('h:i A') : null;
        $eventLocation = $order->event_location;
        
        $message = "*Payment Reminder*\n\n" .
               "Dear *{$order->client_name}*,\n\n" .
               "This is a friendly reminder regarding your pending payment for Order *{$order->order_code}*.\n\n" .
               "*Order Details:*\n" .
               "• Event Period: {$eventFrom} to {$eventTo}\n";
        
        if ($eventTime) {
            $message .= "• Event Time: {$eventTime}\n";
        }
        
        if ($eventLocation) {
            $message .= "• Location: {$eventLocation}\n";
        }
        
        $message .= "• Total Amount: ₹" . number_format($order->total_amount, 2) . "\n" .
               "• *Pending Amount: ₹" . number_format($order->final_payable, 2) . "*\n\n" .
               "Please make the payment at your earliest convenience.\n\n" .
               "Payment can be made via GPay, PhonePe, Paytm, or Bank Transfer.\n\n" .
               "Thank you for your business! 🙏";
        
        return $message;
    }

    /**
     * Get email HTML template
     */
    private function getEmailTemplate(Order $order, string $message)
    {
        $eventFrom = \Carbon\Carbon::parse($order->event_from)->format('l, F j, Y');
        $eventTo = \Carbon\Carbon::parse($order->event_to)->format('l, F j, Y');
        $eventTime = $order->event_time ? \Carbon\Carbon::parse($order->event_time)->format('h:i A') : null;
        $eventLocation = $order->event_location;
        
        return view('emails.payment_reminder', compact('order', 'message', 'eventFrom', 'eventTo', 'eventTime', 'eventLocation'))->render();
    }

    /**
     * Format phone number for WhatsApp (India: +91)
     */
    private function formatPhoneNumber($phone)
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Add country code if not present (India +91)
        if (strlen($phone) == 10) {
            $phone = '91' . $phone;
        }
        
        return $phone;
    }

}
