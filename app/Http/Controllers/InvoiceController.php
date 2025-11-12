<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Dispatch;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $clients = Client::orderBy('name')->get();

        $clientId = $request->get('client_id');
        $month = $request->get('month', now()->format('Y-m'));

        $query = Invoice::with('client');

        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        if ($month) {
            [$year, $m] = explode('-', $month);
            $query->whereMonth('start_date', $m)->whereYear('start_date', $year);
        }

        $invoices = $query->orderBy('start_date', 'desc')->paginate(15);

        return view('invoices.index', compact('invoices', 'clients', 'clientId', 'month'));
    }

    public function generate(Request $request)
    {
        $client = Client::findOrFail($request->client_id);
        $month = $request->month;

        [$year, $m] = explode('-', $month);
        $startDate = Carbon::createFromDate($year, $m, 1);
        $endDate = $startDate->copy()->endOfMonth();

        // Fetch dispatches for client within this month
        $dispatches = Dispatch::where('client_id', $client->id)
            ->whereBetween('dispatch_date', [$startDate, $endDate])
            ->with('items')
            ->get();

        $totalAmount = 0;
        $totalItems = 0;
        $totalDays = 0;

        foreach ($dispatches as $dispatch) {
            foreach ($dispatch->items as $item) {
                $days = $dispatch->dispatch_date->diffInDays($dispatch->expected_return_date, false) + 1;
                $days = $days > 0 ? $days : 1;
                $totalDays += $days;
                $totalItems += $item->quantity;
                $totalAmount += ($item->quantity * $item->rate_per_day * $days);
            }
        }

        // Create invoice record
        $invoiceCode = 'INV-' . $startDate->format('Ym') . '-' . str_pad(Invoice::count() + 1, 3, '0', STR_PAD_LEFT);
        $invoice = Invoice::create([
            'client_id' => $client->id,
            'invoice_code' => $invoiceCode,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_amount' => $totalAmount,
            'total_items' => $totalItems,
            'total_days' => $totalDays,
        ]);

        // Generate PDF
        $pdf = Pdf::loadView('invoices.monthly', [
            'invoice' => $invoice,
            'client' => $client,
            'dispatches' => $dispatches,
        ]);

        $path = "invoices/{$invoice->invoice_code}.pdf";
        Storage::disk('public')->put($path, $pdf->output());

        $invoice->update(['invoice_path' => $path]);

        return redirect()->route('invoices.index')->with('success', 'Invoice generated successfully.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        //
    }
}
