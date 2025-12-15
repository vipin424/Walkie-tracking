<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Client;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller
{
     public function index(Request $request)
    {
        $query = Order::with('client')->where('status', '!=', 'dispatched');

        if ($request->client) {
            $query->where('client_id', $request->client);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(10);
        $clients = Client::all();

        return view('orders.index', compact('orders','clients'));
    }

    public function create()
    {
        $clients = Client::all();
        return view('orders.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required',
            'order_date' => 'required|date',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        DB::transaction(function() use ($request) {

        $yearMonth = now()->format('Ym');

            $lastOrder = Order::where('order_code', 'like', "ORD-$yearMonth-%")
                ->orderBy('id', 'desc')
                ->first();

            if ($lastOrder) {
                $lastSeq = (int) substr($lastOrder->order_code, -3); // last 3 digits
                $nextSeq = $lastSeq + 1;
            } else {
                $nextSeq = 1;
            }

            $code = "ORD-$yearMonth-" . str_pad($nextSeq, 3, '0', STR_PAD_LEFT);
            $reminderDate = Carbon::parse($request->start_date)->subDays(2);

            $order = Order::create([
                'order_code' => $code,
                'client_id' => $request->client_id,
                'order_date' => $request->order_date,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'event_name' => $request->event_name,
                'location' => $request->location,
                'delivery_type' => $request->delivery_type,
                'delivery_charges' => $request->delivery_charges ?? 0,
                'remarks' => $request->remarks,
                'reminder_date' => $reminderDate,
            ]);

            $total = 0;

            foreach ($request->items as $item) {

                $days = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date));
                $months = Carbon::parse($request->start_date)->diffInMonths(Carbon::parse($request->end_date));

                if ($item['rental_type'] === 'monthly') {
                    $itemTotal = $item['quantity'] * $item['rate_per_month'] * ($months ?: 1);
                } else {
                    $itemTotal = $item['quantity'] * $item['rate_per_day'] * ($days ?: 1);
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'item_type' => $item['item_type'],
                    'brand' => $item['brand'],
                    'model' => $item['model'],
                    'quantity' => $item['quantity'],
                    'rental_type' => $item['rental_type'],
                    'rate_per_day' => $item['rate_per_day'] ?? 0,
                    'rate_per_month' => $item['rate_per_month'] ?? 0,
                    'total_amount' => $itemTotal,
                ]);

                $total += $itemTotal;
            }

            OrderPayment::create([
                'order_id' => $order->id,
                'total_amount' => $total,
                'advance_paid' => $request->advance_paid ?? 0,
                'payment_mode' => $request->payment_mode,
                'due_amount' => $total,
            ]);
        });

        return redirect()->route('orders.index')->with('success','Order Added Successfully!');
    }

    public function edit($id)
    {
        $order = Order::with('items')->findOrFail($id);
        $clients = Client::all();

        return view('orders.edit', compact('order','clients'));
    }

   public function show($id)
   {
        $order = Order::with(['client','items','payment'])->findOrFail($id);

        return view('orders.show', compact('order'));
   }

   public function update(Request $request, $id)
   {
        $request->validate([
            'client_id' => 'required',
            'order_date' => 'required|date',
            'start_date' => 'required|date',
            'end_date' => 'required|date'
        ]);

        DB::transaction(function() use ($request, $id) {

            $order = Order::findOrFail($id);

            // Update main order fields
            $order->update([
                'client_id' => $request->client_id,
                'order_date' => $request->order_date,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'event_name' => $request->event_name,
                'location' => $request->location,
                'delivery_type' => $request->delivery_type,
                'delivery_charges' => $request->delivery_charges ?? 0,
                'remarks' => $request->remarks,
                'reminder_date' => Carbon::parse($request->start_date)->subDays(2),
            ]);

            // Delete old items
            OrderItem::where('order_id', $order->id)->delete();

            // Re-add items
            $total = 0;
            foreach ($request->items as $item) {

                $days = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date));
                $months = Carbon::parse($request->start_date)->diffInMonths(Carbon::parse($request->end_date));

                if ($item['rental_type'] === 'monthly') {
                    $itemTotal = $item['quantity'] * $item['rate_per_month'] * ($months ?: 1);
                } else {
                    $itemTotal = $item['quantity'] * $item['rate_per_day'] * ($days ?: 1);
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'item_type' => $item['item_type'],
                    'brand' => $item['brand'],
                    'model' => $item['model'],
                    'quantity' => $item['quantity'],
                    'rental_type' => $item['rental_type'],
                    'rate_per_day' => $item['rate_per_day'] ?? 0,
                    'rate_per_month' => $item['rate_per_month'] ?? 0,
                    'total_amount' => $itemTotal,
                ]);

                $total += $itemTotal;
            }

            // Update payment
            if ($order->payment) {
                $order->payment->update([
                    'total_amount' => $total,
                    'advance_paid' => $request->advance_paid,
                    'due_amount' => $total - $request->advance_paid,
                ]);
            }
        });

        return redirect()->route('orders.index')->with('success', 'Order Updated Successfully');
    }


    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        OrderItem::where('order_id', $order->id)->delete();
        OrderPayment::where('order_id', $order->id)->delete();

        $order->delete();

        return back()->with('success','Order Deleted');
    }


    public function approve($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'approved']);

        return back()->with('success','Order Approved');
    }


    public function reject($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'rejected']);

        return back()->with('success','Order Rejected');
    }


    public function convertToDispatch($id)
    {
        $order = Order::with('items', 'client')->findOrFail($id);

        // Must be approved first
        if ($order->status !== 'approved') {
            return back()->with('error', 'Order not approved yet!');
        }

        // Load clients list for dropdown
        $clients = Client::all();

        return view('dispatches.convert', compact('order', 'clients'));
    }





}
