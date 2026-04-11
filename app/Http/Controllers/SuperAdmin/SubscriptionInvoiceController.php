<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\SubscriptionInvoice;
use Illuminate\Http\Request;

class SubscriptionInvoiceController extends Controller
{
    public function index()
    {
        $invoices = SubscriptionInvoice::with(['company', 'plan'])->latest()->paginate(20);
        return view('super_admin.billing.index', compact('invoices'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id'  => 'required|exists:companies,id',
            'plan_id'     => 'required|exists:plans,id',
            'amount'      => 'required|numeric',
            'period_from' => 'required|date',
            'period_to'   => 'required|date|after:period_from',
        ]);

        $data['invoice_number'] = 'SINV-' . now()->format('Ymd') . '-' . rand(1000, 9999);

        SubscriptionInvoice::create($data);

        return redirect()->route('super.billing.index')->with('success', 'Invoice created.');
    }

    public function markPaid(SubscriptionInvoice $invoice, Request $request)
    {
        $invoice->update([
            'status'         => 'paid',
            'payment_method' => $request->payment_method ?? 'manual',
            'transaction_id' => $request->transaction_id,
            'paid_at'        => now(),
        ]);

        // Extend company subscription
        $invoice->company->update([
            'subscription_expires_at' => $invoice->period_to,
            'plan_id'                 => $invoice->plan_id,
        ]);

        return back()->with('success', 'Invoice marked as paid and subscription extended.');
    }
}
