<?php

namespace App\Console\Commands;

use App\Models\MonthlyInvoice;
use App\Mail\MonthlyInvoiceMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class SendPaymentReminders extends Command
{
    protected $signature = 'invoices:send-reminders';
    protected $description = 'Send payment reminders for unpaid invoices after 7 days';

    public function handle()
    {
        $sevenDaysAgo = Carbon::now()->subDays(7)->startOfDay();
        
        // Get unpaid invoices that were sent 7+ days ago
        $invoices = MonthlyInvoice::with('subscription')
            ->whereIn('status', ['pending', 'sent'])
            ->whereNotNull('sent_at')
            ->where('sent_at', '<=', $sevenDaysAgo)
            ->whereHas('subscription', function($query) {
                $query->where('status', 'active')
                      ->whereNotNull('client_email');
            })
            ->get();
            
        $sent = 0;
        $failed = 0;

        foreach ($invoices as $invoice) {
            try {
                $subscription = $invoice->subscription;
                
                // Check if reminder already sent today
                $lastReminder = $invoice->updated_at;
                if ($lastReminder->isToday()) {
                    $this->info("⊘ Skipped {$invoice->invoice_code} - Reminder already sent today");
                    continue;
                }

                // Generate download URL
                $hash = substr(md5($invoice->id . config('app.key')), 0, 8);
                $url = URL::temporarySignedRoute('monthly-invoice.download', now()->addDays(30), ['hash' => $hash, 'ref' => $invoice->id]);

                // Custom reminder message
                $reminderMessage = "This is a friendly reminder that your invoice is still pending payment. Please process the payment at your earliest convenience.";

                // Send email
                $mail = new MonthlyInvoiceMail($invoice, $url, $reminderMessage);
                $mailInstance = Mail::to($subscription->client_email);
                
                // Add CC emails if configured
                if ($subscription->cc_emails) {
                    $ccEmails = array_filter(array_map('trim', explode(',', $subscription->cc_emails)));
                    if (!empty($ccEmails)) {
                        $mailInstance->cc($ccEmails);
                    }
                }
                
                $mailInstance->send($mail);

                // Update invoice to track reminder sent
                $invoice->touch(); // Updates updated_at timestamp

                $sent++;
                $this->info("✓ Sent reminder for {$invoice->invoice_code} to {$subscription->client_email}");

            } catch (\Exception $e) {
                $failed++;
                $this->error("✗ Failed to send reminder for {$invoice->invoice_code}: {$e->getMessage()}");
            }
        }

        $this->info("\n=== Payment Reminder Summary ===");
        $this->info("Reminders sent: {$sent}");
        if ($failed > 0) {
            $this->warn("Failed: {$failed}");
        }
        if ($sent == 0 && $failed == 0) {
            $this->info("No pending invoices found for reminders.");
        }

        return 0;
    }
}
