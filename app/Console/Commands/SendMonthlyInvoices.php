<?php

namespace App\Console\Commands;

use App\Models\MonthlySubscription;
use App\Models\MonthlyInvoice;
use App\Mail\MonthlyInvoiceMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use PDF;

class SendMonthlyInvoices extends Command
{
    protected $signature = 'invoices:send-monthly';
    protected $description = 'Automatically generate and send monthly invoices via email';

    public function handle()
    {
        $today = Carbon::today();
        
        // Get active subscriptions with today as billing day
        $subscriptions = MonthlySubscription::where('status', 'active')
            ->where('billing_day_of_month', $today->day)
            ->whereNotNull('client_email')
            ->get();
            
        $sent = 0;
        $failed = 0;

        foreach ($subscriptions as $subscription) {
            try {
                // Calculate billing period (previous billing day to current billing day)
                $currentBillingDay = Carbon::create($today->year, $today->month, $subscription->billing_day_of_month);
                $periodFrom = $currentBillingDay->copy()->subMonth();
                $periodTo = $currentBillingDay->copy();

                // Check if invoice already exists for this period
                $invoice = MonthlyInvoice::where('subscription_id', $subscription->id)
                    ->where('billing_period_from', $periodFrom)
                    ->where('billing_period_to', $periodTo)
                    ->first();

                // Create invoice if doesn't exist
                if (!$invoice) {
                    $invoice = MonthlyInvoice::create([
                        'subscription_id' => $subscription->id,
                        'invoice_code' => MonthlyInvoice::generateCode(),
                        'billing_period_from' => $periodFrom,
                        'billing_period_to' => $periodTo,
                        'amount' => $subscription->monthly_amount,
                        'status' => 'pending',
                    ]);
                }

                // Generate PDF if not exists
                if (!$invoice->pdf_path || !Storage::disk('public')->exists(str_replace('storage/', '', $invoice->pdf_path))) {
                    $this->generatePdf($invoice);
                    $invoice->refresh();
                }

                // Generate download URL
                $hash = substr(md5($invoice->id . config('app.key')), 0, 8);
                $url = URL::temporarySignedRoute('monthly-invoice.download', now()->addDays(30), ['hash' => $hash, 'ref' => $invoice->id]);

                // Send email
                $mail = new MonthlyInvoiceMail($invoice, $url, null);
                $mailInstance = Mail::to($subscription->client_email);
                
                // Add CC emails if configured
                if ($subscription->cc_emails) {
                    $ccEmails = array_filter(array_map('trim', explode(',', $subscription->cc_emails)));
                    if (!empty($ccEmails)) {
                        $mailInstance->cc($ccEmails);
                    }
                }
                
                $mailInstance->send($mail);

                // Update invoice status
                $invoice->update(['status' => 'sent', 'sent_at' => now()]);

                $sent++;
                $this->info("✓ Sent invoice {$invoice->invoice_code} to {$subscription->client_email}");

            } catch (\Exception $e) {
                $failed++;
                $this->error("✗ Failed to send invoice for {$subscription->subscription_code}: {$e->getMessage()}");
            }
        }

        $this->info("\n=== Summary ===");
        $this->info("Successfully sent: {$sent}");
        if ($failed > 0) {
            $this->warn("Failed: {$failed}");
        }

        return 0;
    }

    private function generatePdf(MonthlyInvoice $invoice)
    {
        $invoice->load('subscription');
        $html = view('subscriptions.invoice-pdf', compact('invoice'))->render();
        $pdf = PDF::loadHTML($html)->setPaper('a4', 'portrait');

        $fileName = $invoice->invoice_code . '.pdf';
        $path = 'monthly-invoices/'.$fileName;
        Storage::disk('public')->makeDirectory('monthly-invoices');
        Storage::disk('public')->put($path, $pdf->output());
        $invoice->update(['pdf_path' => 'storage/' . $path]);
    }
}
