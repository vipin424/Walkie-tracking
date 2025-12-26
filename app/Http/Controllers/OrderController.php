<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\QuotationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use App\Mail\OrderMailable;
use App\Traits\ImageCompress;
use Str;
use PDF;
use Carbon\Carbon;
use DB;

class OrderController extends Controller
{
    use ImageCompress;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q= Order::query();
        if ($request->filled('search')) {
            $s = $request->search;
            $q->where('code','like',"%{$s}%")
              ->orWhere('client_name','like',"%{$s}%")
              ->orWhere('client_phone','like',"%{$s}%");
        }
        $orders = $q->latest()->paginate(20);
        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('orders.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_name'   => 'required|string',
            'client_email'  => 'nullable|email',
            'client_phone'  => 'nullable|string',

            'event_from' => 'required|date',
            'event_to'   => 'required|date|after_or_equal:event_from',
            'agreement_required' => $request->pickup_type === 'self',

            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_percent' => 'nullable|numeric|min:0',

            'advance_paid' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, &$order) {

            /** 🔹 TOTAL DAYS */
            $eventFrom = Carbon::parse($request->event_from);
            $eventTo   = Carbon::parse($request->event_to);
            $totalDays = $eventFrom->diffInDays($eventTo) + 1;

            $subtotal   = 0;
            $tax_amount = 0;
            $discount   = floatval($request->discount_amount ?? 0);

            /** 🔹 ITEMS CALCULATION (PER DAY) */
            foreach ($request->items as $row) {

                $qty  = floatval($row['quantity']);
                $unit = floatval($row['unit_price']);
                $taxp = floatval($row['tax_percent'] ?? 0);

                $baseTotal = $qty * $unit * $totalDays;
                $lineTax   = $baseTotal * ($taxp / 100);

                $subtotal   += $baseTotal;
                $tax_amount += $lineTax;
            }

            /** 🔹 EXTRA CHARGES */
            $extraChargeType = $request->extra_charge_type;
            $extraRate  = floatval($request->extra_charge_rate ?? 0);
            $extraTotal = 0;

            if ($extraChargeType === 'delivery') {
                $extraTotal = $extraRate; // one time
            }

            if ($extraChargeType === 'staff') {
                $extraTotal = $extraRate * $totalDays; // per day
            }

            /** 🔹 FINAL TOTAL */
            $total = $subtotal + $tax_amount + $extraTotal - $discount;

            /** 🔹 CREATE ORDER */
            $order = Order::create([
                'order_code' => Order::generateCode(),
                'quotation_id' => null, // DIRECT ORDER

                'client_name'  => $request->client_name,
                'client_email' => $request->client_email,
                'client_phone' => $request->client_phone,

                'event_from' => $request->event_from,
                'event_to'   => $request->event_to,
                'notes'      => $request->notes,
                'total_days' => $totalDays,

                'subtotal' => $subtotal,
                'tax_amount' => $tax_amount,

                'extra_charge_type'  => $extraChargeType,
                'extra_charge_rate'  => $extraRate,
                'extra_charge_total' => $extraTotal,

                'discount_amount' => $discount,
                'total_amount' => $total,
                'security_deposit' => floatval($request->security_deposit ?? 0),
                'advance_paid'  => $request->advance_paid,
                'balance_amount'=> $total - $request->advance_paid,
                'agreement_required' => $request->pickup_type === 'self',

                'status' => 'confirmed',
                'created_by' => auth()->id(),
            ]);

            /** 🔹 SAVE ORDER ITEMS */
            foreach ($request->items as $row) {

                $qty  = intval($row['quantity']);
                $unit = floatval($row['unit_price']);
                $taxp = floatval($row['tax_percent'] ?? 0);

                $baseTotal = $qty * $unit * $totalDays;
                $lineTax   = $baseTotal * ($taxp / 100);

                $order->items()->create([
                    'item_name' => $row['item_name'],
                    'item_type' => $row['item_type'] ?? null,
                    'description' => $row['description'] ?? null,
                    'quantity' => $qty,
                    'unit_price' => $unit,
                    'tax_percent' => $taxp,
                    'total_price' => $baseTotal + $lineTax,
                ]);
            }
        });

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Order created successfully.');
    }

    
    public function complete(Request $request, Order $order)
    {
        $request->validate([
            'damage_charge' => 'nullable|numeric|min:0',
            'late_fee'      => 'nullable|numeric|min:0',
        ]);

        $damage = floatval($request->damage_charge ?? 0);
        $late   = floatval($request->late_fee ?? 0);

        $deposit = floatval($order->security_deposit);
        $balance = floatval($order->balance_amount);

        $depositRemaining = $deposit - ($damage + $late);

        if ($depositRemaining >= 0) {
            // deposit covers damages
            $finalPayable = $balance - $depositRemaining;

            if ($finalPayable <= 0) {
                $refund = abs($finalPayable);
                $finalPayable = 0;
            } else {
                $refund = 0;
            }

        } else {
            // deposit not enough
            $finalPayable = $balance + abs($depositRemaining);
            $refund = 0;
        }
 
        $order->update([
            'damage_charge'   => $damage,
            'late_fee'        => $late,
            'deposit_adjusted'=> max($depositRemaining, 0),
            'refund_amount'   => $refund,
            'final_payable'   => $finalPayable,
            'settlement_status'=>'settled',
            'settlement_date' => now(),
        ]);

        return back()->with('success','Settlement completed.');
    }

    // public function complete(Request $request, Order $order)
    // {
    //     $request->validate([
    //         'damage_charge' => 'nullable|numeric|min:0',
    //         'late_fee'      => 'nullable|numeric|min:0',
    //     ]);

    //     $damage = floatval($request->damage_charge ?? 0);
    //     $late   = floatval($request->late_fee ?? 0);

    //     // remaining rent still unpaid
    //     $remaining = $order->balance_amount;

    //     // adjust from deposit
    //     $depositRemaining = $order->security_deposit - ($remaining + $damage + $late);

    //     if ($depositRemaining >= 0) {
    //         // refund to client
    //         $order->refund_amount = $depositRemaining;
    //         $order->amount_due    = 0;
    //     } else {
    //         // customer still owes
    //         $order->refund_amount = 0;
    //         $order->amount_due    = abs($depositRemaining);
    //     }

    //     $order->update([
    //         'damage_charge'  => $damage,
    //         'late_fee'       => $late,
    //         'settlement_status' => 'pending',
    //         'status'             => 'completed',
    //     ]);

    //     return redirect()->route('orders.show',$order)
    //         ->with('success',"Order marked completed. Please proceed with settlement.");
    // }


    public function settle(Order $order)
    {
        $order->update([
            'settlement_status' => 'settled',
        ]);

        return redirect()->route('orders.show',$order)
            ->with('success','Settlement completed. Final invoice ready.');
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

    //     $order = Order::create([
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
    //             'quotation_id' => $order->id,
    //             'item_name' => $row['item_name'],
    //             'item_type' => $row['item_type'] ?? null,
    //             'description' => $row['description'] ?? null,
    //             'quantity' => $qty,
    //             'unit_price' => $unit,
    //             'tax_percent' => $taxp,
    //             'total_price' => $lineTotal + $lineTax,
    //         ]);
    //     }

    //     return redirect()->route('quotations.show', $quotation)->with('success','Order created. You can generate PDF or send now.');
    // }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load('items');
        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        $order->load('items');
        return view('orders.edit', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
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
            $order->update([
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
                'security_deposit' => floatval($request->security_deposit ?? 0),
                'advance_paid'  => $request->advance_paid,
                'balance_amount'=> $total - $request->advance_paid
            ]);

            /** 🔹 REPLACE ITEMS */
            $order->items()->delete();

            foreach ($request->items as $row) {

                $qty  = intval($row['quantity']);
                $unit = floatval($row['unit_price']);
                $taxp = floatval($row['tax_percent'] ?? 0);

                $baseTotal = $qty * $unit * $totalDays;
                $lineTax   = $baseTotal * ($taxp / 100);

                OrderItem::create([
                    'order_id' => $order->id,
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
                ->route('orders.show', $order)
                ->with('success', "Order updated successfully for {$totalDays} day(s).");
        }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $order->delete();
        return redirect()->route('orders.index')->with('success','Order deleted.');
    }

    // Generate PDF and store it
    public function generatePdf(Order $order)
    {
        $order->load('items');
        $html = view('orders.pdf', compact('order'))->render();
        $pdf = PDF::loadHTML($html)->setPaper('a4', 'portrait');

        $fileName = $order->order_code . '.pdf';
        Storage::disk('public')->put('orders/'.$fileName, $pdf->output());


        // Save public accessible path like storage/orders/...
        $order->pdf_path = 'storage/orders/' . $fileName;
        $order->save();

        // log
        // OrderLog::create([
        //     'order_id' => $order->id,
        //     'user_id' => Auth::id(),
        //     'action' => 'generated_pdf',
        //     'meta' => json_encode(['path' => $order->pdf_path]),
        // ]);

        return redirect()->back()->with('success','PDF generated and stored.');
    }

    // Download via signed route - signed middleware protects it
    public function downloadPdf(Order $order)
    {
        if (!$order->pdf_path) {
            abort(404, 'PDF not found');
        }
        // Convert storage path to actual path
        $diskPath = str_replace('storage/','public/',$order->pdf_path);
        if (!Storage::exists($diskPath)) abort(404,'File not found');
        return Storage::download($diskPath, $order->code . '.pdf');
    }

    public function sendEmail(Request $request, Order $order)
    {
        $request->validate([
            'to_email' => 'required|email',
            'message' => 'nullable|string'
        ]);

        // ensure PDF exists, if not generate
        if (!$order->pdf_path || !Storage::exists(str_replace('storage/','public/',$order->pdf_path))) {
            // generate
            $this->generatePdf($order);
        }

        Mail::to($request->to_email)->send(new OrderMailable($order, $request->message));

        $order->update(['status' => 'sent']);
        QuotationLog::create([
            'quotation_id' => $order->id,
            'user_id' => Auth::id(),
            'action' => 'sent_email',
            'meta' => json_encode(['to' => $request->to_email]),
        ]);

        return redirect()->back()->with('success','Order emailed successfully.');
    }

    public function sendWhatsapp(Request $request, Order $order)
    {
        $request->validate([
            'to_phone' => 'required|string',
            'message'  => 'nullable|string',
        ]);

        // Ensure PDF exists
        if (
            !$order->pdf_path ||
            !Storage::exists(str_replace('storage/', 'public/', $order->pdf_path))
        ) {
            $this->generatePdf($order);
            $order->refresh();
        }

        $url = \URL::signedRoute(
            'orders.download',
            ['order' => $order->id],
            now()->addDays(7)
        );

        $messageText =
            "Hello {$order->client_name},\n\n" .
            "Here is the order {$order->code}.\n\n" .
            "Total Amount: ₹" . number_format($order->total_amount, 2) . "\n\n" .
            "Download Order:\n{$url}\n\n" .
            "Please reply to confirm.";

        $phone = preg_replace('/\D+/', '', $request->to_phone);
        if (strlen($phone) <= 10) {
            $phone = '91' . $phone;
        }

        $waLink = 'https://wa.me/' . $phone . '?text=' . rawurlencode($messageText);

        QuotationLog::create([
            'quotation_id' => $order->id,
            'user_id'      => Auth::id(),
            'action'       => 'sent_whatsapp_link',
            'meta'         => json_encode(['to' => $phone, 'link' => $url]),
        ]);

        return redirect($waLink);
    }



    public function download(Order $order)
    {
        // Ensure PDF exists
        if (
            !$order->pdf_path ||
            !Storage::exists(str_replace('storage/', 'public/', $order->pdf_path))
        ) {
            abort(404, 'Order PDF not found.');
        }

        $path = str_replace('storage/', 'public/', $order->pdf_path);

        return Storage::download(
            $path,
            $order->code . '.pdf'
        );
    }

    public function generateAgreement(Order $order)
    {
        abort_if(!$order->agreement_required, 403);

        $agreement = $order->agreement()->create([
            'agreement_code' => 'AGR-' . Str::upper(Str::random(10)),
            'expires_at' => now()->addHours(48),
        ]);

        $signedUrl = URL::temporarySignedRoute(
            'agreement.sign',
            $agreement->expires_at,
            ['code' => $agreement->agreement_code]
        );

        return $signedUrl;
    }

    public function uploadAadhaar(Request $request, Order $order)
    {
       
        $agreement = $order->agreement;

        // abort_if(!$agreement || $agreement->status !== 'signed', 403);

        $request->validate([
            'aadhaar_type' => 'required|in:front_back,full',

            'aadhaar_front' => 'required_if:aadhaar_type,front_back|image|mimes:jpg,jpeg,png|max:5120',
            'aadhaar_back'  => 'required_if:aadhaar_type,front_back|image|mimes:jpg,jpeg,png|max:5120',

            'aadhaar_full'  => 'required_if:aadhaar_type,full|image|mimes:jpg,jpeg,png|max:5120',
        ]);
        
        $data = [
            'aadhaar_uploaded_at' => now(),
            'aadhaar_uploaded_by' => auth()->id(),
            'aadhaar_status' => 'uploaded',
        ];

        if ($request->aadhaar_type === 'front_back') {
            $data['aadhaar_front'] = $this->compressAndStore($request->aadhaar_front);
            $data['aadhaar_back']  = $this->compressAndStore($request->aadhaar_back);
        }

        if ($request->aadhaar_type === 'full') {
            $data['aadhaar_full'] = $this->compressAndStore($request->aadhaar_full);
        }

        $agreement->update($data);

        return back()->with('success', 'Aadhaar uploaded successfully.');
    }



    // public function storeFromQuotation(Request $request, Order $order)
    // {
    //     $request->validate([
    //         'advance_paid' => 'required|numeric|min:0|max:' . $order->total_amount,
    //     ]);

    //     DB::transaction(function () use ($order, $request, &$order) {

    //         $order = Order::create([
    //             'order_code' => Order::generateCode(),
    //             'quotation_id' => $order->id,

    //             'client_name' => $order->client_name,
    //             'client_email'=> $order->client_email,
    //             'client_phone'=> $order->client_phone,

    //             'event_from' => $order->event_from,
    //             'event_to'   => $order->event_to,
    //             'total_days' => $order->total_days,

    //             'subtotal' => $order->subtotal,
    //             'tax_amount' => $order->tax_amount,

    //             'extra_charge_type' => $order->extra_charge_type,
    //             'extra_charge_rate' => $order->extra_charge_rate,
    //             'extra_charge_total'=> $order->extra_charge_total,

    //             'discount_amount' => $order->discount_amount,
    //             'total_amount' => $order->total_amount,

    //             'advance_paid' => $request->advance_paid,
    //             'balance_amount' => $order->total_amount - $request->advance_paid,

    //             'status' => 'confirmed',
    //             'created_by' => auth()->id(),
    //         ]);

    //         foreach ($order->items as $item) {
    //             $order->items()->create($item->toArray());
    //         }

    //         $order->update(['status' => 'accepted']);
    //     });

    //     return redirect()->route('orders.show', $order)
    //         ->with('success','Order converted to Order.');
    // }





}
