<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderAgreement;
use App\Models\Client;
use App\Models\Quotation;
use App\Models\OrderItem;
use App\Models\OrderLog;
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
        $q = Order::query();

        // 🔍 Search logic (unchanged, but grouped safely)
        if ($request->filled('search')) {
            $s = $request->search;

            $q->where(function ($query) use ($s) {
                $query->where('order_code', 'like', "%{$s}%")
                    ->orWhere('client_name', 'like', "%{$s}%")
                    ->orWhere('client_phone', 'like', "%{$s}%");
            });
        }

        // 📄 Fetch paginated orders
        $orders = $q->latest()->paginate(20);

        /**
         * 🗓️ ADD EVENT DATE LOGIC
         * - total days
         * - days left
         * - event state (upcoming / running / completed)
         */
        $orders->getCollection()->transform(function ($order) {

            $today = Carbon::today();
            $eventFrom = Carbon::parse($order->event_from)->startOfDay();
            $eventTo   = Carbon::parse($order->event_to)->startOfDay();

            $order->event_days = $order->total_days
                ?? ($eventFrom->diffInDays($eventTo) + 1);

            if ($today->lt($eventFrom)) {
                // UPCOMING
                $diff = $today->diffInDays($eventFrom);

                $order->event_state = 'upcoming';
                if ((int) $diff === 1) {
                    $order->days_label = 'Tomorrow';
                } else {
                    $order->days_label = $diff . ' days to start';
                }

            } elseif ($today->between($eventFrom, $eventTo)) {
                // RUNNING
                $diff = $today->diffInDays($eventTo);

                $order->event_state = 'running';

                if ((int) $diff === 0) {
                    $order->days_label = 'Ends today';
                } elseif ((int) $diff === 1) {
                    $order->days_label = '1 day left';
                } else {
                    $order->days_label = $diff . ' days left';
                }

            } else {
                // COMPLETED
                $order->event_state = 'completed';
                $order->days_label = 'Completed';
            }

            return $order;
        });

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
        // dd($request->all());
        $request->validate([
            'client_name'   => 'required|string',
            'client_email'  => 'nullable|email',
            'client_phone'  => 'nullable|string',

            'event_from' => 'required|date',
            'event_to'   => 'required|date|after_or_equal:event_from',
            'handle_type' => 'required|string',
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
                'handle_type' => $request->handle_type === 'self' ? 1 : 0,
                'notes'      => $request->notes,
                'bill_to'    => $request->bill_to,
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
                'agreement_required' => $request->handle_type === 'self',

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
                'handle_type' => 'required|string',

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
                'handle_type' => $request->handle_type === 'self' ? 1 : 0,
                'total_days' => $totalDays,
                'notes' => $request->notes,
                'bill_to'    => $request->bill_to,
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
    public function destroy(Order $order)
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
        // ✅ Signed URL (7 days valid)
        $downloadUrl = URL::temporarySignedRoute(
            'orders.download',
            now()->addDays(7),
            ['order' => $order->id]
        );

        Mail::to($request->to_email)->send(new OrderMailable($order, $request->message, $downloadUrl));

        $order->update(['status' => 'sent']);
        OrderLog::create([
            'order_id' => $order->id,
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

        /** ✅ Ensure PDF exists */
        if (
            !$order->pdf_path ||
            !Storage::disk('public')->exists($order->pdf_path)
        ) {
            $this->generatePdf($order);
            $order->refresh();
        }

        /** ✅ Generate signed download URL (7 days valid) */
        $url = URL::temporarySignedRoute(
            'orders.download',
            now()->addDays(7),
            ['order' => $order->id]
        );
        /** ✅ WhatsApp message */
        $messageText =
            "Hello *{$order->client_name}*,\n\n" .
            "Your order *{$order->order_code}* is confirmed.\n\n" .
            "📅 *Event Date:* " .
            \Carbon\Carbon::parse($order->event_from)->format('d M Y') .
            " to " .
            \Carbon\Carbon::parse($order->event_to)->format('d M Y') . "\n\n" .
            "Total Amount: ₹" . number_format($order->total_amount, 2) . "\n\n" .
            "Download Order PDF:\n{$url}\n\n" .
            "This link is valid for 7 days.\n\n" .  
            "– *Crewrent Enterprises*";


        /** ✅ Normalize phone number */
        $phone = preg_replace('/\D+/', '', $request->to_phone);
        if (strlen($phone) <= 10) {
            $phone = '91' . $phone;
        }

        $waLink = 'https://wa.me/' . $phone . '?text=' . rawurlencode($messageText);

        /** ✅ Log */
        OrderLog::create([
            'order_id' => $order->id,
            'user_id'      => Auth::id(),
            'action'       => 'sent_whatsapp_link',
            'meta'         => json_encode([
                'to'   => $phone,
                'link' => $url,
            ]),
        ]);

        return redirect($waLink);
    }

    public function download(Order $order)
    {
        if (!request()->hasValidSignature()) {
            abort(403, 'This download link has expired or is invalid.');
        }

        if (!$order->pdf_path) {
            abort(404, 'Order PDF not found.');
        }

        $filePath = storage_path(
            'app/public/' . str_replace('storage/', '', $order->pdf_path)
        );

        if (!file_exists($filePath)) {
            abort(404, 'Order PDF not found.');
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$order->order_code.'.pdf"',
        ]);
    }


    public function generateAgreement(Order $order)
    {
        abort_if(!$order->agreement_required, 403, 'Agreement not required for this order');

        $agreement = $order->agreement()->updateOrCreate(
            ['order_id' => $order->id], // condition
            [
                'agreement_code' => 'AGR-' . strtoupper(Str::random(10)),
                'expires_at'     => now()->addHours(48),
                'status'         => 'pending',
            ]
        );

        /**
         * ✅ Generate temporary signed URL
         */
        $signedUrl = URL::temporarySignedRoute(
            'agreement.sign',
            $agreement->expires_at,
            ['code' => $agreement->agreement_code]
        );

        $agreement->update(['signed_url' => $signedUrl]);

        return redirect()->back()->with('success', 'Agreement url generated successfully.');
    }

    public function uploadAadhaar(Request $request, Order $order)
    {
        // ✅ Validate request
        $request->validate([
            'aadhaar_type' => 'required|in:front_back,full',

            'aadhaar_front' => 'required_if:aadhaar_type,front_back|image|mimes:jpg,jpeg,png|max:5120',
            'aadhaar_back'  => 'required_if:aadhaar_type,front_back|image|mimes:jpg,jpeg,png|max:5120',

            'aadhaar_full'  => 'required_if:aadhaar_type,full|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        /**
         * ✅ GET OR CREATE AGREEMENT
         * Agar agreement exist nahi karta → create
         * Agar exist karta → wahi use hoga
         */
        $agreement = $order->agreement()->firstOrCreate(
            ['order_id' => $order->id],
            [
                'agreement_code' => 'AGR-' . strtoupper(\Str::random(10)), 
                'expires_at' => now()->addHours(48),
            ]
        );

        /**
         * ✅ Prepare update data
         */
        $data = [
            'aadhaar_uploaded_at' => now(),
            'aadhaar_uploaded_by' => auth()->id(),
            'aadhaar_status'      => 'uploaded',
        ];

        /**
         * ✅ Aadhaar upload logic
         */
        if ($request->aadhaar_type === 'front_back') {

            $data['aadhaar_front'] = $this->compressAndStore($request->file('aadhaar_front'));
            $data['aadhaar_back']  = $this->compressAndStore($request->file('aadhaar_back'));

            // clear full if previously uploaded
            $data['aadhaar_full'] = null;
        }

        if ($request->aadhaar_type === 'full') {

            $data['aadhaar_full'] = $this->compressAndStore($request->file('aadhaar_full'));

            // clear front/back if previously uploaded
            $data['aadhaar_front'] = null;
            $data['aadhaar_back']  = null;
        }

        /**
         * ✅ Update agreement
         */
        $agreement->update($data);

        return back()->with('success', 'Aadhaar uploaded successfully.');
    }




    public function storeFromQuotation(Request $request, Quotation $quotation)
    {
        $request->validate([
            'advance_paid' => 'required|numeric|min:0|max:' . $quotation->total_amount,
        ]);

        DB::transaction(function () use ($quotation, $request, &$order) {

            /** ✅ CREATE ORDER */
            $order = Order::create([
                'order_code'   => Order::generateCode(),
                'quotation_id' => $quotation->id,

                'client_name'  => $quotation->client_name,
                'client_email' => $quotation->client_email,
                'client_phone' => $quotation->client_phone,

                'event_from' => $quotation->event_from,
                'event_to'   => $quotation->event_to,
                'handle_type' => $quotation->handle_type,
                'notes'      => $quotation->notes,
                'bill_to'    => $quotation->bill_to,
                'total_days' => $quotation->total_days,

                'subtotal' => $quotation->subtotal,
                'tax_amount' => $quotation->tax_amount,

                'extra_charge_type'  => $quotation->extra_charge_type,
                'extra_charge_rate'  => $quotation->extra_charge_rate,
                'extra_charge_total' => $quotation->extra_charge_total,

                'discount_amount' => $quotation->discount_amount,
                'total_amount'    => $quotation->total_amount,

                'advance_paid' => $request->advance_paid,
                'balance_amount' =>
                    $quotation->total_amount - $request->advance_paid,

                'status'     => 'confirmed',
                'created_by' => auth()->id(),
                'agreement_required' => $quotation->handle_type,
            ]);

            /** ✅ COPY ITEMS */
            foreach ($quotation->items as $item) {
                $order->items()->create($item->toArray());
            }

            /** ✅ UPDATE QUOTATION STATUS */
            $quotation->update([
                'status' => 'accepted'
            ]);
        });

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Quotation converted to Order successfully.');
    }






}
