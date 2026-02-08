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
            $remainingAmount = max(0, $order->balance_amount - $totalPaid);

            
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
                    ->subject('Payment Reminder - Order ' . $order->order_code)
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
        
        return "*Payment Reminder*\n\n" .
               "Dear *{$order->client_name}*,\n\n" .
               "This is a friendly reminder regarding your pending payment for Order *{$order->order_code}*.\n\n" .
               "*Order Details:*\n" .
               "• Event Period: {$eventFrom} to {$eventTo}\n" .
               "• Total Amount: ₹" . number_format($order->total_amount, 2) . "\n" .
               "• *Pending Amount: ₹" . number_format($order->final_payable, 2) . "*\n\n" .
               "Please make the payment at your earliest convenience.\n\n" .
               "Payment can be made via GPay, PhonePe, Paytm, or Bank Transfer.\n\n" .
               "Thank you for your business! 🙏";
    }

    /**
     * Get email HTML template
     */
    private function getEmailTemplate(Order $order, string $message)
    {
        $eventFrom = \Carbon\Carbon::parse($order->event_from)->format('d M Y');
        $eventTo = \Carbon\Carbon::parse($order->event_to)->format('d M Y');
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { 
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                    line-height: 1.6; 
                    color: #333;
                    background-color: #f4f4f4;
                    margin: 0;
                    padding: 0;
                }
                .container { 
                    max-width: 600px; 
                    margin: 30px auto; 
                    background: white;
                    border-radius: 10px;
                    overflow: hidden;
                    box-shadow: 0 0 20px rgba(0,0,0,0.1);
                }
                .header { 
                    background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
                    color: #000; 
                    padding: 30px 20px; 
                    text-align: center; 
                }
                .header h1 {
                    margin: 0;
                    font-size: 28px;
                    font-weight: 600;
                }
                .content { 
                    padding: 30px; 
                }
                .message {
                    background: #f9f9f9;
                    padding: 20px;
                    border-radius: 5px;
                    margin: 20px 0;
                    white-space: pre-line;
                }
                .details { 
                    background: white; 
                    padding: 20px; 
                    margin: 20px 0; 
                    border-left: 4px solid #ffc107;
                    border-radius: 5px;
                }
                .details h3 {
                    margin-top: 0;
                    color: #ff9800;
                }
                .detail-row {
                    display: flex;
                    justify-content: space-between;
                    padding: 8px 0;
                    border-bottom: 1px solid #eee;
                }
                .detail-row:last-child {
                    border-bottom: none;
                }
                .detail-label {
                    font-weight: 600;
                    color: #666;
                }
                .detail-value {
                    color: #333;
                }
                .amount { 
                    font-size: 32px; 
                    font-weight: bold; 
                    color: #ff5722;
                    text-align: center;
                    margin: 20px 0;
                    padding: 20px;
                    background: #fff3e0;
                    border-radius: 10px;
                }
                .footer { 
                    background: #f9f9f9;
                    text-align: center; 
                    padding: 20px; 
                    font-size: 13px; 
                    color: #666; 
                    border-top: 1px solid #eee;
                }
                .payment-methods {
                    display: flex;
                    justify-content: center;
                    gap: 15px;
                    margin: 20px 0;
                    flex-wrap: wrap;
                }
                .payment-method {
                    padding: 8px 15px;
                    background: #e3f2fd;
                    border-radius: 20px;
                    font-size: 13px;
                    color: #1976d2;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>💰 Payment Reminder</h1>
                </div>
                <div class='content'>
                    <p>Dear <strong>{$order->client_name}</strong>,</p>
                    
                    <div class='message'>" . nl2br(htmlspecialchars($message)) . "</div>
                    
                    <div class='details'>
                        <h3>📋 Order Details</h3>
                        <div class='detail-row'>
                            <span class='detail-label'>Order Code:</span>
                            <span class='detail-value'><strong>{$order->order_code}</strong></span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Event Period:</span>
                            <span class='detail-value'>{$eventFrom} to {$eventTo}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Total Amount:</span>
                            <span class='detail-value'>₹" . number_format($order->total_amount, 2) . "</span>
                        </div>
                    </div>
                    
                    <div class='amount'>
                        Pending Amount<br>
                        ₹" . number_format($order->final_payable, 2) . "
                    </div>
                    
                    <div style='text-align: center; margin-top: 20px;'>
                        <p style='margin-bottom: 10px; color: #666;'>Accept payments via:</p>
                        <div class='payment-methods'>
                            <span class='payment-method'>💳 GPay</span>
                            <span class='payment-method'>📱 PhonePe</span>
                            <span class='payment-method'>💰 Paytm</span>
                            <span class='payment-method'>🏦 Bank Transfer</span>
                        </div>
                    </div>
                    
                    <p style='margin-top: 30px; font-size: 14px; color: #666;'>
                        If you have already made the payment, please ignore this reminder or contact us with your transaction details.
                    </p>
                </div>
                <div class='footer'>
                    <p style='margin: 5px 0;'><strong>This is an automated reminder</strong></p>
                    <p style='margin: 5px 0;'>Please do not reply to this email</p>
                    <p style='margin: 5px 0; font-size: 12px; color: #999;'>© " . date('Y') . " " . config('app.name') . ". All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
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
