<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReturnRequest;
use App\Models\Dispatch;
use App\Models\DispatchItem;
use App\Models\ReturnEntry;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{

    public function store(ReturnRequest $request)
    {
        $data = $request->validated();
        $dispatchId = null;

        DB::transaction(function() use ($data, &$dispatchId) {

            $dispatch = Dispatch::lockForUpdate()->with('items', 'payment')->findOrFail($data['dispatch_id']);
            $dispatchId = $dispatch->id;

            $dispatchDate = \Carbon\Carbon::parse($dispatch->dispatch_date);
            $totalAmount = 0;

            foreach ($data['items'] as $itemData) {

                /** @var \App\Models\DispatchItem $item */
                $item = DispatchItem::where('dispatch_id', $dispatch->id)
                        ->lockForUpdate()
                        ->findOrFail($itemData['dispatch_item_id']);

                $pending = $item->quantity - $item->returned_qty;
                $ret = (int)$itemData['returned_qty'];

                if ($ret > $pending) {
                    abort(422, "Return qty cannot exceed pending qty for item {$item->id}");
                }

                $returnDate = \Carbon\Carbon::parse($data['return_date']);
                $daysUsed = $dispatchDate->diffInDays($returnDate, false);
                $daysUsed = max($daysUsed, 1);

                // Update returned qty
                $item->returned_qty += $ret;
                $item->save();

                // Create log entry
                ReturnEntry::create([
                    'dispatch_id' => $dispatch->id,
                    'dispatch_item_id' => $item->id,
                    'returned_qty' => $ret,
                    'return_date' => $returnDate,
                    'remarks' => $data['remarks'] ?? null,
                ]);

                // Partial total for this return
                $itemTotal = $ret * $item->rate_per_day * $daysUsed;
                $item->total_amount += $itemTotal;
                $item->save();

                $totalAmount += $itemTotal;
            }

            if ($dispatch->payment) {
                $dispatch->payment->increment('total_amount', $totalAmount);
            }

            $dispatch->recalcStatusAndTotals();
        });

        // ✅ After DB transaction is complete
        DB::afterCommit(function() use ($dispatchId) {
            $dispatch = Dispatch::with(['client', 'items', 'payment'])->find($dispatchId);

            if ($dispatch && $dispatch->status === Dispatch::STATUS_RETURNED) {
                $dispatch->generateInvoicePDF();
            }
        });

        return back()->with('success', 'Return updated successfully and invoice generated.');
    }



}
