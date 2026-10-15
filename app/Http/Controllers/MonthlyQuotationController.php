<?php

namespace App\Http\Controllers;

use App\Models\MonthlyQuotation;
use App\Models\MonthlyQuotationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MonthlyQuotationController extends Controller
{
    /* ─────────────────────── INDEX ─────────────────────── */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = MonthlyQuotation::orderByDesc('id');

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            return datatables()->eloquent($query)
                ->addColumn('code_link', fn($q) =>
                    '<a href="' . route('monthly-quotations.show', $q) . '" class="text-primary fw-semibold">' . $q->code . '</a>')
                ->addColumn('client', fn($q) =>
                    '<div><strong>' . e($q->client_name) . '</strong>' .
                    ($q->client_company ? '<br><small class="text-muted">' . e($q->client_company) . '</small>' : '') .
                    '</div>')
                ->addColumn('total', fn($q) => '₹' . number_format($q->total_amount, 2))
                ->addColumn('status_badge', fn($q) => $q->status_badge)
                ->addColumn('created', fn($q) => $q->created_at->format('d M Y'))
                ->addColumn('actions', function ($q) {
                    return '
                        <div class="btn-group">
                            <a href="' . route('monthly-quotations.show', $q) . '" class="btn btn-sm btn-outline-primary" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="' . route('monthly-quotations.edit', $q) . '" class="btn btn-sm btn-outline-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="' . route('monthly-quotations.generate-pdf', $q) . '" method="POST" class="d-inline">
                                <input type="hidden" name="_token" value="' . csrf_token() . '">
                                <button class="btn btn-sm btn-outline-danger" title="Generate PDF">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </button>
                            </form>
                        </div>';
                })
                ->rawColumns(['code_link', 'client', 'status_badge', 'actions'])
                ->make(true);
        }

        return view('monthly-quotations.index');
    }

    /* ─────────────────────── CREATE ─────────────────────── */
    public function create()
    {
        return view('monthly-quotations.create');
    }

    /* ─────────────────────── STORE ─────────────────────── */
    public function store(Request $request)
    {
        $request->validate([
            'client_name'       => 'required|string|max:255',
            'client_company'    => 'nullable|string|max:255',
            'client_email'      => 'nullable|email|max:255',
            'client_phone'      => 'nullable|string|max:30',
            'client_address'    => 'nullable|string',
            'client_gstin'      => 'nullable|string|max:20',
            'validity_days'     => 'required|integer|min:1',
            'payment_terms'     => 'nullable|string',
            'delivery_timeline' => 'nullable|string|max:255',
            'gst_percent'       => 'required|numeric|min:0|max:100',
            'notes'             => 'nullable|string',
            'items'             => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.uom'       => 'required|string|max:20',
            'items.*.quantity'  => 'required|numeric|min:0.01',
            'items.*.rate'      => 'required|numeric|min:0',
        ]);

        // Build bill_to block
        $billTo = $this->buildBillTo($request);

        // Calculate totals
        [$subtotal, $gstAmount, $total] = $this->calculateTotals($request->items, $request->gst_percent);

        $quotation = MonthlyQuotation::create([
            'code'              => MonthlyQuotation::generateCode(),
            'client_name'       => $request->client_name,
            'client_company'    => $request->client_company,
            'client_email'      => $request->client_email,
            'client_phone'      => $request->client_phone,
            'client_address'    => $request->client_address,
            'client_gstin'      => $request->client_gstin,
            'bill_to'           => $billTo,
            'validity_days'     => $request->validity_days,
            'payment_terms'     => $request->payment_terms,
            'delivery_timeline' => $request->delivery_timeline,
            'subtotal'          => $subtotal,
            'gst_percent'       => $request->gst_percent,
            'gst_amount'        => $gstAmount,
            'total_amount'      => $total,
            'notes'             => $request->notes,
            'status'            => 'draft',
            'created_by'        => auth()->user()->name ?? 'Admin',
        ]);

        $this->saveItems($quotation, $request->items);

        return redirect()->route('monthly-quotations.show', $quotation)
            ->with('success', 'Monthly quotation created successfully.');
    }

    /* ─────────────────────── SHOW ─────────────────────── */
    public function show(MonthlyQuotation $monthlyQuotation)
    {
        $monthlyQuotation->load('items');
        return view('monthly-quotations.show', compact('monthlyQuotation'));
    }

    /* ─────────────────────── EDIT ─────────────────────── */
    public function edit(MonthlyQuotation $monthlyQuotation)
    {
        $monthlyQuotation->load('items');
        return view('monthly-quotations.edit', compact('monthlyQuotation'));
    }

    /* ─────────────────────── UPDATE ─────────────────────── */
    public function update(Request $request, MonthlyQuotation $monthlyQuotation)
    {
        $request->validate([
            'client_name'       => 'required|string|max:255',
            'client_company'    => 'nullable|string|max:255',
            'client_email'      => 'nullable|email|max:255',
            'client_phone'      => 'nullable|string|max:30',
            'client_address'    => 'nullable|string',
            'client_gstin'      => 'nullable|string|max:20',
            'validity_days'     => 'required|integer|min:1',
            'payment_terms'     => 'nullable|string',
            'delivery_timeline' => 'nullable|string|max:255',
            'gst_percent'       => 'required|numeric|min:0|max:100',
            'notes'             => 'nullable|string',
            'status'            => 'required|in:draft,sent',
            'items'             => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.uom'       => 'required|string|max:20',
            'items.*.quantity'  => 'required|numeric|min:0.01',
            'items.*.rate'      => 'required|numeric|min:0',
        ]);

        $billTo = $this->buildBillTo($request);
        [$subtotal, $gstAmount, $total] = $this->calculateTotals($request->items, $request->gst_percent);

        $monthlyQuotation->update([
            'client_name'       => $request->client_name,
            'client_company'    => $request->client_company,
            'client_email'      => $request->client_email,
            'client_phone'      => $request->client_phone,
            'client_address'    => $request->client_address,
            'client_gstin'      => $request->client_gstin,
            'bill_to'           => $billTo,
            'validity_days'     => $request->validity_days,
            'payment_terms'     => $request->payment_terms,
            'delivery_timeline' => $request->delivery_timeline,
            'subtotal'          => $subtotal,
            'gst_percent'       => $request->gst_percent,
            'gst_amount'        => $gstAmount,
            'total_amount'      => $total,
            'notes'             => $request->notes,
            'status'            => $request->status,
        ]);

        // Delete old items and re-save
        $monthlyQuotation->items()->delete();
        $this->saveItems($monthlyQuotation, $request->items);

        return redirect()->route('monthly-quotations.show', $monthlyQuotation)
            ->with('success', 'Monthly quotation updated successfully.');
    }

    /* ─────────────────────── DELETE ─────────────────────── */
    public function destroy(MonthlyQuotation $monthlyQuotation)
    {
        // Delete PDF if exists
        if ($monthlyQuotation->pdf_path) {
            $path = str_replace('storage/', '', $monthlyQuotation->pdf_path);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
        $monthlyQuotation->items()->delete();
        $monthlyQuotation->delete();
        return redirect()->route('monthly-quotations.index')
            ->with('success', 'Quotation deleted successfully.');
    }

    /* ─────────────────────── GENERATE PDF ─────────────────────── */
    public function generatePdf(MonthlyQuotation $monthlyQuotation)
    {
        $monthlyQuotation->load('items');

        $upiId   = config('payment.upi.id');
        $upiName = config('payment.upi.name');
        $upiUrl  = "upi://pay?pa={$upiId}&pn=" . urlencode($upiName) .
                   "&am={$monthlyQuotation->total_amount}&cu=INR&tn=" .
                   urlencode("Payment for {$monthlyQuotation->code}");
        $qrCode  = base64_encode(QrCode::format('png')->size(200)->generate($upiUrl));

        $html = view('monthly-quotations.pdf', compact('monthlyQuotation', 'qrCode'))->render();
        $pdf  = PDF::loadHTML($html)->setPaper('a4', 'portrait');

        $fileName = $monthlyQuotation->code . '.pdf';
        $dir      = 'monthly-quotations';
        Storage::disk('public')->makeDirectory($dir);
        Storage::disk('public')->put("{$dir}/{$fileName}", $pdf->output());

        $monthlyQuotation->update([
            'pdf_path' => "storage/{$dir}/{$fileName}",
            'status'   => $monthlyQuotation->status === 'draft' ? 'draft' : $monthlyQuotation->status,
        ]);

        return redirect()->route('monthly-quotations.show', $monthlyQuotation)
            ->with('success', 'PDF generated successfully.');
    }

    /* ─────────────────────── HELPERS ─────────────────────── */
    private function buildBillTo(Request $request): string
    {
        $parts = [];
        if ($request->client_company) $parts[] = '<strong>' . e($request->client_company) . '</strong>';
        if ($request->client_name)    $parts[] = e($request->client_name);
        if ($request->client_address) $parts[] = nl2br(e($request->client_address));
        if ($request->client_gstin)   $parts[] = 'GSTIN: ' . e($request->client_gstin);
        if ($request->client_phone)   $parts[] = 'Ph: ' . e($request->client_phone);
        if ($request->client_email)   $parts[] = 'Email: ' . e($request->client_email);
        return implode('<br>', $parts);
    }

    private function calculateTotals(array $items, float $gstPercent): array
    {
        $subtotal  = collect($items)->sum(fn($item) => ($item['quantity'] ?? 0) * ($item['rate'] ?? 0));
        $gstAmount = round($subtotal * $gstPercent / 100, 2);
        $total     = round($subtotal + $gstAmount, 2);
        return [$subtotal, $gstAmount, $total];
    }

    private function saveItems(MonthlyQuotation $quotation, array $items): void
    {
        foreach ($items as $i => $item) {
            $qty    = (float)($item['quantity'] ?? 0);
            $rate   = (float)($item['rate'] ?? 0);
            $amount = round($qty * $rate, 2);

            MonthlyQuotationItem::create([
                'monthly_quotation_id' => $quotation->id,
                'item_name'            => $item['item_name'],
                'description'          => $item['description'] ?? null,
                'uom'                  => $item['uom'] ?? 'MTH',
                'quantity'             => $qty,
                'rate'                 => $rate,
                'amount'               => $amount,
                'sort_order'           => $i,
            ]);
        }
    }
}
