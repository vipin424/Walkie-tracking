<?php

namespace App\Http\Controllers;

use App\Http\Requests\DispatchRequest;
use App\Models\Client;
use App\Models\Dispatch;
use App\Models\DispatchItem;
use App\Models\Payment;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use lluminate\Support\Facades\Log;

class DispatchController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getDataTable($request);
        }
        return view('dispatches.index');
    }

    private function getDataTable($request)
    {
        $query = Dispatch::with(['client','payment'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('client'), fn($q) => $q->whereHas('client', fn($qq) => $qq->where('name','like',"%{$request->client}%")))
            ->orderByDesc('id');

        return datatables()->eloquent($query)
            ->addColumn('code', function ($d) {
                return '<a href="'.route('dispatches.show', $d).'" class="text-decoration-none fw-semibold text-primary"><i class="bi bi-truck me-2"></i>'.$d->code.'</a>';
            })
            ->addColumn('client', function ($d) {
                return '<div class="d-flex align-items-center"><div class="bg-warning bg-opacity-10 rounded-circle p-2 me-2"><i class="bi bi-person-fill text-warning"></i></div><div><span class="fw-medium d-block">'.$d->client->name.'</span><small class="text-muted">'.$d->client->phone.'</small></div></div>';
            })
            ->addColumn('dispatch_date', function ($d) {
                return Carbon::parse($d->dispatch_date)->format('d M Y');
            })
            ->addColumn('return_date', function ($d) {
                return $d->expected_return_date ? Carbon::parse($d->expected_return_date)->format('d M Y') : '<span class="text-muted">-</span>';
            })
            ->addColumn('items', function ($d) {
                return '<span class="badge bg-info bg-opacity-10 text-info">'.$d->total_items.' Items</span>';
            })
            ->addColumn('status', function ($d) {
                $colors = ['active' => 'success', 'returned' => 'secondary', 'partial' => 'warning'];
                $color = $colors[$d->status] ?? 'secondary';
                return '<span class="badge bg-'.$color.' px-3 py-2">'.ucfirst($d->status).'</span>';
            })
            ->addColumn('payment', function ($d) {
                $colors = ['unpaid' => 'danger', 'partial' => 'warning', 'paid' => 'success'];
                $color = $colors[$d->payment->payment_status ?? 'unpaid'] ?? 'secondary';
                return '<span class="badge bg-'.$color.' px-3 py-2">'.ucfirst($d->payment->payment_status ?? 'unpaid').'</span>';
            })
            ->addColumn('actions', function ($d) {
                $html = '<div class="btn-group" role="group">';
                $html .= '<a href="'.route('dispatches.show', $d).'" class="btn btn-sm btn-outline-primary" title="View"><i class="bi bi-eye"></i></a>';
                $html .= '<button class="btn btn-sm btn-outline-danger" onclick="deleteDispatch('.$d->id.')" title="Delete"><i class="bi bi-trash"></i></button>';
                $html .= '</div>';
                return $html;
            })
            ->rawColumns(['code', 'client', 'return_date', 'items', 'status', 'payment', 'actions'])
            ->make(true);
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

            $advance = 0;

            // IF COMING FROM ORDER, FETCH ORDER ADVANCE
            if (!empty($data['order_id'])) {
                $order = Order::with('payment')->find($data['order_id']);
                $advance = $order->payment->advance_paid ?? 0;
            }

            // CREATE PAYMENT ENTRY
            Payment::create([
                'dispatch_id' => $dispatch->id,
                'payment_status' => \App\Models\Payment::STATUS_UNPAID,
                'advance_amount' => $advance,
                'total_amount' => 0,
                'remarks' => null,
            ]);

            // UPDATE ORDER STATUS
            if (!empty($data['order_id'])) {
                $order->status = 'dispatched';
                $order->save();
            }
           
            // if ($data['order_id']) {
            //     $order = Order::find($data['order_id']);
            //     $order->status = 'dispatched';
            //     $order->save();
            // }

            // Payment::create([
            //     'dispatch_id' => $dispatch->id,
            //     'payment_status' => \App\Models\Payment::STATUS_UNPAID,
            //     'advance_amount' => 0,
            //     'total_amount' => 0,
            //     'remarks' => null,
            // ]);



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
        return response()->json(['success' => true, 'message' => 'Dispatch deleted successfully.']);
    }
}
