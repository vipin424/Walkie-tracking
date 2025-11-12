<?php

namespace App\Http\Controllers;

use App\Http\Requests\DispatchRequest;
use App\Models\Client;
use App\Models\Dispatch;
use App\Models\DispatchItem;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use lluminate\Support\Facades\Log;

class DispatchController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $client = $request->get('client');
        $dispatches = Dispatch::with(['client','payment'])
            ->when($status, fn($q)=>$q->where('status',$status))
            ->when($client, fn($q)=>$q->whereHas('client', fn($qq)=>$qq->where('name','like',"%$client%")))
            ->orderByDesc('id')
            ->paginate(10);

        return view('dispatches.index', compact('dispatches','status','client'));
    }

    public function create()
    {
        $clients = Client::orderBy('name')->get();
        return view('dispatches.create', compact('clients'));
    }

    public function store(DispatchRequest $request)
    {
        $data = $request->validated();

        return DB::transaction(function() use ($data) {
            $today = now()->format('Ymd');
            $seq = (Dispatch::whereDate('created_at', now()->toDateString())->count() + 1);
            $code = "DSP-{$today}-" . str_pad($seq, 3, '0', STR_PAD_LEFT);

            $dispatch = Dispatch::create([
                'code' => $code,
                'client_id' => $data['client_id'],
                'dispatch_date' => $data['dispatch_date'],
                'expected_return_date' => $data['expected_return_date'] ?? null,
                'status' => Dispatch::STATUS_ACTIVE,
                'total_items' => 0,
            ]);


            foreach ($data['items'] as $item) {

                DispatchItem::create([
                    'dispatch_id' => $dispatch->id,
                    'item_type' => $item['item_type'],
                    'brand' => $item['brand'] ?? null,
                    'model' => $item['model'] ?? null,
                    'quantity' => $item['quantity'],
                    'returned_qty' => 0,
                    'rental_type' => $item['rental_type'] ?? 'daily',
                    'rate_per_day' => $item['rate_per_day'] ?? 0,
                    'rate_per_month' => $item['rate_per_month'] ?? 0,

                  ]);
            }


            $dispatch->recalcStatusAndTotals();

            Payment::create([
                'dispatch_id' => $dispatch->id,
                'payment_status' => \App\Models\Payment::STATUS_UNPAID,
                'advance_amount' => 0,
                'total_amount' => 0,
                'remarks' => null,
            ]);

            return redirect()->route('dispatches.show', $dispatch)->with('success','Dispatch created');
        });
    }

    public function show(Dispatch $dispatch)
    {
        $dispatch->load(['client','items','payment','returns']);
        return view('dispatches.show', compact('dispatch'));
    }

    public function destroy(Dispatch $dispatch)
    {
        $dispatch->delete();
        return redirect()->route('dispatches.index')->with('success','Dispatch deleted');
    }
}
