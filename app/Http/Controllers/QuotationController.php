<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\QuotationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\QuotationMailable;
use PDF;
use Carbon\Carbon;


class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = Quotation::query();
        if ($request->filled('search')) {
            $s = $request->search;
            $q->where('code','like',"%{$s}%")
              ->orWhere('client_name','like',"%{$s}%")
              ->orWhere('client_phone','like',"%{$s}%");
        }
        $quotations = $q->latest()->paginate(20);
        return view('quotations.index', compact('quotations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('quotations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_name' => 'required|string',
            'client_email' => 'nullable|email',
            'client_phone' => 'nullable|string',

            'event_from' => 'required|date',
            'event_to'   => 'required|date|after_or_equal:event_from',

            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_percent' => 'nullable|numeric|min:0',
        ]);

        /** 🔹 CALCULATE TOTAL DAYS */
        $eventFrom = Carbon::parse($request->event_from);
        $eventTo   = Carbon::parse($request->event_to);

        // +1 because same day = 1 day
        $totalDays = $eventFrom->diffInDays($eventTo) + 1;

        $subtotal = 0;
        $tax_amount = 0;
        $discount = floatval($request->discount_amount ?? 0);

        /** 🔹 CALCULATE TOTALS (PER DAY LOGIC) */
        foreach ($request->items as $row) {

            $qty   = floatval($row['quantity']);
            $unit  = floatval($row['unit_price']);
            $taxp  = floatval($row['tax_percent'] ?? 0);

            // ⭐ PER DAY BASE TOTAL
            $baseTotal = $qty * $unit * $totalDays;

            $lineTax   = $baseTotal * ($taxp / 100);
            $lineTotal = $baseTotal + $lineTax;

            $subtotal  += $baseTotal;
            $tax_amount += $lineTax;
        }
        // EXTRA CHARGES
        $extraChargeType = $request->extra_charge_type;
        $extraRate = floatval($request->extra_charge_rate ?? 0);
        $extraTotal = 0;

        if ($extraChargeType === 'delivery') {
            $extraTotal = $extraRate; // one time
        }

        if ($extraChargeType === 'staff') {
            $extraTotal = $extraRate * $totalDays; // per day
        }


        $total = $subtotal + $tax_amount + $extraTotal - $discount;

        /** 🔹 SAVE QUOTATION */
        $quotation = Quotation::create([
            'client_name' => $request->client_name,
            'client_email' => $request->client_email,
            'client_phone' => $request->client_phone,
            'event_from' => $request->event_from,
            'event_to' => $request->event_to,
            'total_days' => $totalDays,
            'notes' => $request->notes,
            'subtotal' => $subtotal,
            'tax_amount' => $tax_amount,
            'discount_amount' => $discount,
            'extra_charge_type'  => $extraChargeType,
            'extra_charge_rate'  => $extraRate,
            'extra_charge_total' => $extraTotal,
            'total_amount' => $total,
            'created_by' => Auth::id(),
            'status' => 'draft',
        ]);

        /** 🔹 SAVE ITEMS (PER DAY TOTAL STORED) */
        foreach ($request->items as $row) {

            $qty   = intval($row['quantity']);
            $unit  = floatval($row['unit_price']);
            $taxp  = floatval($row['tax_percent'] ?? 0);

            $baseTotal = $qty * $unit * $totalDays;
            $lineTax   = $baseTotal * ($taxp / 100);

            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'item_name' => $row['item_name'],
                'item_type' => $row['item_type'] ?? null,
                'description' => $row['description'] ?? null,
                'quantity' => $qty,
                'unit_price' => $unit,
                'tax_percent' => $taxp,
                'total_price' => $baseTotal + $lineTax,
            ]);
        }

        return redirect()
            ->route('quotations.show', $quotation)
            ->with('success', "Quotation created for {$totalDays} day(s). You can generate PDF or send now.");
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'client_name' => 'required|string',
    //         'client_email' => 'nullable|email',
    //         'client_phone' => 'nullable|string',
            
    //         'items' => 'required|array|min:1',
    //         'items.*.item_name' => 'required|string',
    //         'items.*.quantity' => 'required|numeric|min:1',
    //         'items.*.unit_price' => 'required|numeric|min:0',
    //         'items.*.tax_percent' => 'nullable|numeric|min:0',
    //     ]);

    //     // calculate totals
    //     $subtotal = 0;
    //     $tax_amount = 0;
    //     $discount = floatval($request->discount_amount ?? 0);

    //     foreach ($request->items as $row) {
    //         $qty = floatval($row['quantity'] ?? 1);
    //         $unit = floatval($row['unit_price'] ?? 0);
    //         $taxp = floatval($row['tax_percent'] ?? 0);
    //         $lineTotal = $qty * $unit;
    //         $lineTax = $lineTotal * ($taxp/100);
    //         $subtotal += $lineTotal + $lineTax;
    //         $tax_amount += $lineTax;
    //     }

    //     $total = $subtotal - $discount;

    //     $quotation = Quotation::create([
    //         'client_name'=> $request->client_name,
    //         'client_email'=> $request->client_email,
    //         'client_phone'=> $request->client_phone,
    //         'event_from'=> $request->event_from ?: null,
    //         'event_to'=> $request->event_to ?: null,
    //         'notes'=> $request->notes ?: null,
    //         'subtotal'=> $subtotal,
    //         'tax_amount'=> $tax_amount,
    //         'discount_amount'=> $discount,
    //         'total_amount'=> $total,
    //         'created_by'=> Auth::id(),
    //         'status' => 'draft',
    //     ]);

    //     // save items
    //     foreach ($request->items as $row) {
    //         $qty = intval($row['quantity'] ?? 1);
    //         $unit = floatval($row['unit_price'] ?? 0);
    //         $taxp = floatval($row['tax_percent'] ?? 0);
    //         $lineTotal = $qty * $unit;
    //         $lineTax = $lineTotal * ($taxp/100);
    //         QuotationItem::create([
    //             'quotation_id' => $quotation->id,
    //             'item_name' => $row['item_name'],
    //             'item_type' => $row['item_type'] ?? null,
    //             'description' => $row['description'] ?? null,
    //             'quantity' => $qty,
    //             'unit_price' => $unit,
    //             'tax_percent' => $taxp,
    //             'total_price' => $lineTotal + $lineTax,
    //         ]);
    //     }

    //     return redirect()->route('quotations.show', $quotation)->with('success','Quotation created. You can generate PDF or send now.');
    // }

    /**
     * Display the specified resource.
     */
    public function show(Quotation $quotation)
    {
        $quotation->load('items','logs');
        return view('quotations.show', compact('quotation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quotation $quotation)
    {
        $quotation->load('items');
        return view('quotations.edit', compact('quotation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quotation $quotation)
    {
            $request->validate([
                'client_name' => 'required|string',
                'client_email' => 'nullable|email',
                'client_phone' => 'nullable|string',

                'event_from' => 'required|date',
                'event_to'   => 'required|date|after_or_equal:event_from',

                'items' => 'required|array|min:1',
                'items.*.item_name' => 'required|string',
                'items.*.quantity' => 'required|numeric|min:1',
                'items.*.unit_price' => 'required|numeric|min:0',
                'items.*.tax_percent' => 'nullable|numeric|min:0',
            ]);

            /** 🔹 CALCULATE TOTAL DAYS */
            $eventFrom = Carbon::parse($request->event_from);
            $eventTo   = Carbon::parse($request->event_to);

            // Same day booking = 1 day
            $totalDays = $eventFrom->diffInDays($eventTo) + 1;

            $subtotal = 0;
            $tax_amount = 0;
            $discount = floatval($request->discount_amount ?? 0);

            /** 🔹 RECALCULATE TOTALS (PER DAY) */
            foreach ($request->items as $row) {

                $qty  = floatval($row['quantity']);
                $unit = floatval($row['unit_price']);
                $taxp = floatval($row['tax_percent'] ?? 0);

                $baseTotal = $qty * $unit * $totalDays;
                $lineTax   = $baseTotal * ($taxp / 100);

                $subtotal   += $baseTotal;
                $tax_amount += $lineTax;
            }

            // EXTRA CHARGES
            $extraChargeType = $request->extra_charge_type;
            $extraRate = floatval($request->extra_charge_rate ?? 0);
            $extraTotal = 0;

            if ($extraChargeType == 'delivery') {
                $extraTotal = floatval($request->delivery_charge_amount ?? 0); // one time
            }

            if ($extraChargeType == 'staff') {
                $extraTotal = $extraRate * $totalDays; // per day
            }


            $total = $subtotal + $tax_amount + $extraTotal - $discount;
            //$total = $subtotal + $tax_amount - $discount;

            /** 🔹 UPDATE QUOTATION */
            $quotation->update([
                'client_name' => $request->client_name,
                'client_email' => $request->client_email,
                'client_phone' => $request->client_phone,
                'event_from' => $request->event_from,
                'event_to' => $request->event_to,
                'total_days' => $totalDays,
                'notes' => $request->notes,
                'subtotal' => $subtotal,
                'tax_amount' => $tax_amount,
                'discount_amount' => $discount,
                'extra_charge_type'  => $extraChargeType,
                'extra_charge_rate'  => $extraRate,
                'extra_charge_total' => $extraTotal,
                'total_amount' => $total,
            ]);

            /** 🔹 REPLACE ITEMS */
            $quotation->items()->delete();

            foreach ($request->items as $row) {

                $qty  = intval($row['quantity']);
                $unit = floatval($row['unit_price']);
                $taxp = floatval($row['tax_percent'] ?? 0);

                $baseTotal = $qty * $unit * $totalDays;
                $lineTax   = $baseTotal * ($taxp / 100);

                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'item_name' => $row['item_name'],
                    'item_type' => $row['item_type'] ?? null,
                    'description' => $row['description'] ?? null,
                    'quantity' => $qty,
                    'unit_price' => $unit,
                    'tax_percent' => $taxp,
                    'total_price' => $baseTotal + $lineTax,
                ]);
            }

            return redirect()
                ->route('quotations.show', $quotation)
                ->with('success', "Quotation updated successfully for {$totalDays} day(s).");
        }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $quotation->delete();
        return redirect()->route('quotations.index')->with('success','Quotation deleted.');
    }

    // Generate PDF and store it
    public function generatePdf(Quotation $quotation)
    {
        $quotation->load('items');
        $html = view('quotations.pdf', compact('quotation'))->render();
        $pdf = PDF::loadHTML($html)->setPaper('a4', 'portrait');

        $fileName = $quotation->code . '.pdf';
        Storage::disk('public')->put('quotations/'.$fileName, $pdf->output());


        // Save public accessible path like storage/quotations/...
        $quotation->pdf_path = 'storage/quotations/' . $fileName;
        $quotation->save();

        // log
        QuotationLog::create([
            'quotation_id' => $quotation->id,
            'user_id' => Auth::id(),
            'action' => 'generated_pdf',
            'meta' => json_encode(['path' => $quotation->pdf_path]),
        ]);

        return redirect()->back()->with('success','PDF generated and stored.');
    }

    // Download via signed route - signed middleware protects it
    public function downloadPdf(Quotation $quotation)
    {
        if (!$quotation->pdf_path) {
            abort(404, 'PDF not found');
        }
        // Convert storage path to actual path
        $diskPath = str_replace('storage/','public/',$quotation->pdf_path);
        if (!Storage::exists($diskPath)) abort(404,'File not found');
        return Storage::download($diskPath, $quotation->code . '.pdf');
    }

    public function sendEmail(Request $request, Quotation $quotation)
    {
        $request->validate([
            'to_email' => 'required|email',
            'message' => 'nullable|string'
        ]);

        // ensure PDF exists, if not generate
        if (!$quotation->pdf_path || !Storage::exists(str_replace('storage/','public/',$quotation->pdf_path))) {
            // generate
            $this->generatePdf($quotation);
        }

        Mail::to($request->to_email)->send(new QuotationMailable($quotation, $request->message));

        $quotation->update(['status' => 'sent']);
        QuotationLog::create([
            'quotation_id' => $quotation->id,
            'user_id' => Auth::id(),
            'action' => 'sent_email',
            'meta' => json_encode(['to' => $request->to_email]),
        ]);

        return redirect()->back()->with('success','Quotation emailed successfully.');
    }

    public function sendWhatsapp(Request $request, Quotation $quotation)
    {
        $request->validate([
            'to_phone' => 'required|string',
            'message'  => 'nullable|string',
        ]);

        // Ensure PDF exists
        if (
            !$quotation->pdf_path ||
            !Storage::exists(str_replace('storage/', 'public/', $quotation->pdf_path))
        ) {
            $this->generatePdf($quotation);
            $quotation->refresh();
        }

        $url = \URL::signedRoute(
            'quotations.download',
            ['quotation' => $quotation->id],
            now()->addDays(7)
        );

        $messageText =
            "Hello {$quotation->client_name},\n\n" .
            "Here is the quotation {$quotation->code}.\n\n" .
            "Total Amount: ₹" . number_format($quotation->total_amount, 2) . "\n\n" .
            "Download Quotation:\n{$url}\n\n" .
            "Please reply to confirm.";

        $phone = preg_replace('/\D+/', '', $request->to_phone);
        if (strlen($phone) <= 10) {
            $phone = '91' . $phone;
        }

        $waLink = 'https://wa.me/' . $phone . '?text=' . rawurlencode($messageText);

        QuotationLog::create([
            'quotation_id' => $quotation->id,
            'user_id'      => Auth::id(),
            'action'       => 'sent_whatsapp_link',
            'meta'         => json_encode(['to' => $phone, 'link' => $url]),
        ]);

        return redirect($waLink);
    }



    public function download(Quotation $quotation)
    {
        // Ensure PDF exists
        if (
            !$quotation->pdf_path ||
            !Storage::exists(str_replace('storage/', 'public/', $quotation->pdf_path))
        ) {
            abort(404, 'Quotation PDF not found.');
        }

        $path = str_replace('storage/', 'public/', $quotation->pdf_path);

        return Storage::download(
            $path,
            $quotation->code . '.pdf'
        );
    }
}
