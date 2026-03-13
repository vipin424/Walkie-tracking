<?php

namespace App\Http\Controllers;

use App\Models\MonthlySubscription;
use App\Models\MonthlyInvoice;
use App\Models\Client;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MonthlySubscriptionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = MonthlySubscription::with('client')
                ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
                ->orderByDesc('id');

            return datatables()->eloquent($query)
                ->addColumn('subscription_code', fn($sub) => '<a href="'.route('subscriptions.show', $sub).'" class="text-primary fw-semibold">'.$sub->subscription_code.'</a>')
                ->addColumn('client', fn($sub) => '<div><strong>'.$sub->client_name.'</strong><br><small>'.$sub->client_phone.'</small></div>')
                ->addColumn('billing_info', fn($sub) => 'Day '.$sub->billing_day_of_month.' of month<br><small>Since: '.$sub->billing_start_date->format('d M Y').'</small>')
                ->addColumn('amount', fn($sub) => '₹'.number_format($sub->monthly_amount, 2))
                ->addColumn('status', function($sub) {
                    $colors = ['active' => 'success', 'paused' => 'warning', 'cancelled' => 'danger'];
                    return '<span class="badge bg-'.$colors[$sub->status].'">'.ucfirst($sub->status).'</span>';
                })
                ->addColumn('actions', function($sub) {
                    return '<div class="btn-group">
                        <a href="'.route('subscriptions.show', $sub).'" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                        <a href="'.route('subscriptions.edit', $sub).'" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                        <button class="btn btn-sm btn-outline-success" onclick="generateInvoice('.$sub->id.')"><i class="bi bi-file-earmark-plus"></i></button>
                    </div>';
                })
                ->rawColumns(['subscription_code', 'client', 'billing_info', 'status', 'actions'])
                ->make(true);
        }
        return view('subscriptions.index');
    }

    public function create()
    {
        $clients = Client::orderBy('name')->get();
        return view('subscriptions.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'billing_details' => 'nullable|string',
            'billing_start_date' => 'required|date',
            'billing_day_of_month' => 'required|integer|min:1|max:28',
            'monthly_amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
        ]);

        $client = Client::findOrFail($request->client_id);

        MonthlySubscription::create([
            'subscription_code' => MonthlySubscription::generateCode(),
            'client_id' => $client->id,
            'client_name' => $client->name,
            'client_email' => $client->email ?? null,
            'client_phone' => $client->contact_number,
            'cc_emails' => $request->cc_emails,
            'billing_details' => $request->billing_details,
            'billing_start_date' => $request->billing_start_date,
            'billing_day_of_month' => $request->billing_day_of_month,
            'monthly_amount' => $request->monthly_amount,
            'items_json' => $request->items,
            'notes' => $request->notes,
            'status' => 'active',
        ]);

        return redirect()->route('subscriptions.index')->with('success', 'Monthly subscription created successfully.');
    }

    public function show(MonthlySubscription $subscription)
    {
        $subscription->load('invoices');
        return view('subscriptions.show', compact('subscription'));
    }

    public function edit(MonthlySubscription $subscription)
    {
        $clients = Client::orderBy('name')->get();
        return view('subscriptions.edit', compact('subscription', 'clients'));
    }

    public function update(Request $request, MonthlySubscription $subscription)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'billing_details' => 'nullable|string',
            'billing_day_of_month' => 'required|integer|min:1|max:28',
            'monthly_amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'status' => 'required|in:active,paused,cancelled',
        ]);

        $client = Client::findOrFail($request->client_id);

        $subscription->update([
            'client_id' => $client->id,
            'client_name' => $client->name,
            'client_email' => $client->email ?? null,
            'client_phone' => $client->contact_number,
            'cc_emails' => $request->cc_emails,
            'billing_details' => $request->billing_details,
            'billing_day_of_month' => $request->billing_day_of_month,
            'monthly_amount' => $request->monthly_amount,
            'items_json' => $request->items,
            'notes' => $request->notes,
            'status' => $request->status,
        ]);

        return redirect()->route('subscriptions.show', $subscription)->with('success', 'Subscription updated successfully.');
    }

    public function generateInvoice(MonthlySubscription $subscription)
    {
        $billingDay = $subscription->billing_day_of_month;
        $today = Carbon::today();
        
        $periodFrom = Carbon::create($today->year, $today->month, $billingDay);
        if ($periodFrom->gt($today)) {
            $periodFrom->subMonth();
        }
        $periodTo = $periodFrom->copy()->addMonth()->subDay();

        // Check if invoice already exists for this billing period
        $existingInvoice = MonthlyInvoice::where('subscription_id', $subscription->id)
            ->where('billing_period_from', $periodFrom)
            ->where('billing_period_to', $periodTo)
            ->first();

        if ($existingInvoice) {
            // Regenerate PDF for existing invoice
            $this->generatePdf($existingInvoice);
            return redirect()->route('subscriptions.show', $subscription)
                ->with('success', 'Invoice for this period already exists. PDF has been regenerated.');
        }

        // Create new invoice
        $invoice = MonthlyInvoice::create([
            'subscription_id' => $subscription->id,
            'invoice_code' => MonthlyInvoice::generateCode(),
            'billing_period_from' => $periodFrom,
            'billing_period_to' => $periodTo,
            'amount' => $subscription->monthly_amount,
            'status' => 'pending',
        ]);

        $this->generatePdf($invoice);

        return redirect()->route('subscriptions.show', $subscription)->with('success', 'Invoice generated successfully.');
    }

    private function generatePdf(MonthlyInvoice $invoice)
    {
        $invoice->load('subscription');
        $html = view('subscriptions.invoice-pdf', compact('invoice'))->render();
        $pdf = PDF::loadHTML($html)->setPaper('a4', 'portrait');

        $fileName = $invoice->invoice_code . '.pdf';
        Storage::disk('public')->put('monthly-invoices/'.$fileName, $pdf->output());

        $invoice->update(['pdf_path' => 'storage/monthly-invoices/' . $fileName]);
    }

    public function sendInvoice(Request $request, MonthlyInvoice $invoice)
    {
        $request->validate([
            'method' => 'required|in:email,whatsapp',
            'to_email' => 'required_if:method,email|email',
            'cc_emails' => 'nullable|string',
            'message' => 'nullable|string',
        ]);

        if (!$invoice->pdf_path || !Storage::disk('public')->exists(str_replace('storage/', '', $invoice->pdf_path))) {
            $this->generatePdf($invoice);
            $invoice->refresh();
        }

        $hash = substr(md5($invoice->id . config('app.key')), 0, 8);
        $url = URL::temporarySignedRoute('monthly-invoice.download', now()->addDays(30), ['hash' => $hash, 'ref' => $invoice->id]);

        if ($request->method === 'email') {
            $mail = new \App\Mail\MonthlyInvoiceMail($invoice, $url, $request->message);
            
            // Build mail
            $mailInstance = \Mail::to($request->to_email);
            
            // Add CC emails if provided
            if ($request->filled('cc_emails')) {
                $ccEmails = array_filter(array_map('trim', explode(',', $request->cc_emails)));
                if (!empty($ccEmails)) {
                    $mailInstance->cc($ccEmails);
                }
            }
            
            // Send mail
            $mailInstance->send($mail);
            
            $invoice->update(['status' => 'sent', 'sent_at' => now()]);
            return back()->with('success', 'Invoice sent via email successfully.');
        }

        if ($request->method === 'whatsapp') {
            $message = "Hello *{$invoice->subscription->client_name}*,\n\n" .
                "Your monthly invoice *{$invoice->invoice_code}* is ready.\n\n" .
                "📅 *Billing Period:* {$invoice->billing_period_from->format('d M Y')} to {$invoice->billing_period_to->format('d M Y')}\n\n" .
                "💰 *Amount:* ₹" . number_format($invoice->amount, 2) . "\n\n" .
                "Download Invoice:\n{$url}\n\n" .
                "– *Crewrent Enterprises*";

            $phone = preg_replace('/\D+/', '', $invoice->subscription->client_phone);
            if (strlen($phone) <= 10) $phone = '91' . $phone;

            $invoice->update(['status' => 'sent', 'sent_at' => now()]);
            return redirect('https://wa.me/' . $phone . '?text=' . rawurlencode($message));
        }

        return back()->with('success', 'Invoice sent successfully.');
    }

    public function sendReminder(Request $request, MonthlyInvoice $invoice)
    {
        $request->validate([
            'method' => 'required|in:email,whatsapp',
            'to_email' => 'required_if:method,email|email',
            'cc_emails' => 'nullable|string',
            'message' => 'nullable|string',
        ]);

        if (!$invoice->pdf_path || !Storage::disk('public')->exists(str_replace('storage/', '', $invoice->pdf_path))) {
            $this->generatePdf($invoice);
            $invoice->refresh();
        }

        $hash = substr(md5($invoice->id . config('app.key')), 0, 8);
        $url = URL::temporarySignedRoute('monthly-invoice.download', now()->addDays(30), ['hash' => $hash, 'ref' => $invoice->id]);

        $reminderMessage = $request->message ?: 'This is a friendly reminder that your invoice is still pending payment. Please process the payment at your earliest convenience.';

        if ($request->method === 'email') {
            $mail = new \App\Mail\MonthlyInvoiceMail($invoice, $url, $reminderMessage);
            
            $mailInstance = \Mail::to($request->to_email);
            
            if ($request->filled('cc_emails')) {
                $ccEmails = array_filter(array_map('trim', explode(',', $request->cc_emails)));
                if (!empty($ccEmails)) {
                    $mailInstance->cc($ccEmails);
                }
            }
            
            $mailInstance->send($mail);
            
            return back()->with('success', 'Payment reminder sent via email successfully.');
        }

        if ($request->method === 'whatsapp') {
            $message = "⚠️ *Payment Reminder*\n\n" .
                "Hello *{$invoice->subscription->client_name}*,\n\n" .
                "{$reminderMessage}\n\n" .
                "📄 *Invoice:* {$invoice->invoice_code}\n" .
                "📅 *Billing Period:* {$invoice->billing_period_from->format('d M Y')} to {$invoice->billing_period_to->format('d M Y')}\n" .
                "💰 *Amount Due:* ₹" . number_format($invoice->amount, 2) . "\n\n" .
                "Download Invoice:\n{$url}\n\n" .
                "– *Crewrent Enterprises*";

            $phone = preg_replace('/\D+/', '', $invoice->subscription->client_phone);
            if (strlen($phone) <= 10) $phone = '91' . $phone;

            return redirect('https://wa.me/' . $phone . '?text=' . rawurlencode($message));
        }

        return back()->with('success', 'Payment reminder sent successfully.');
    }

    public function downloadInvoice($hash, Request $request)
    {
        if (!$request->hasValidSignature()) abort(403, 'Link expired or invalid.');

        $invoiceId = $request->get('ref');
        $expectedHash = substr(md5($invoiceId . config('app.key')), 0, 8);
        
        if ($hash !== $expectedHash) abort(403, 'Invalid link.');

        $invoice = MonthlyInvoice::findOrFail($invoiceId);
        $filePath = storage_path('app/public/' . str_replace('storage/', '', $invoice->pdf_path));

        if (!file_exists($filePath)) abort(404, 'Invoice not found.');

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$invoice->invoice_code.'.pdf"',
        ]);
    }

    public function markPaid(MonthlyInvoice $invoice)
    {
        $invoice->update(['status' => 'paid', 'paid_at' => now()]);
        
        // Create payment transaction
        PaymentTransaction::create([
            'payable_type' => MonthlyInvoice::class,
            'payable_id' => $invoice->id,
            'amount' => $invoice->amount,
            'payment_method' => 'bank_transfer',
            'notes' => 'Monthly subscription invoice payment',
            'paid_at' => now(),
            'recorded_by' => auth()->user()->name ?? 'Admin',
        ]);
        
        return back()->with('success', 'Invoice marked as paid and payment recorded.');
    }

    public function deleteInvoice(MonthlyInvoice $invoice)
    {
        // Delete PDF file if exists
        if ($invoice->pdf_path && Storage::disk('public')->exists(str_replace('storage/', '', $invoice->pdf_path))) {
            Storage::disk('public')->delete(str_replace('storage/', '', $invoice->pdf_path));
        }
        
        $invoice->delete();
        return back()->with('success', 'Invoice deleted successfully.');
    }

    public function getClientData($id)
    {
        $client = Client::findOrFail($id);
        return response()->json([
            'name' => $client->name,
            'phone' => $client->contact_number,
            'email' => $client->email ?? '',
            'company' => $client->company_name ?? ''
        ]);
    }
}
