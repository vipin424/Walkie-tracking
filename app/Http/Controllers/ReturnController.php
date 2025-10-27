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

        DB::transaction(function() use ($data) {
            $dispatch = Dispatch::lockForUpdate()->findOrFail($data['dispatch_id']);

            foreach ($data['items'] as $itemData) {
                $item = DispatchItem::where('dispatch_id',$dispatch->id)
                        ->lockForUpdate()
                        ->findOrFail($itemData['dispatch_item_id']);

                $pending = $item->quantity - $item->returned_qty;
                $ret = (int)$itemData['returned_qty'];
                if ($ret > $pending) {
                    abort(422, "Return qty cannot exceed pending qty for item {$item->id}");
                }

                $item->returned_qty += $ret;
                $item->save();

                ReturnEntry::create([
                    'dispatch_id' => $dispatch->id,
                    'dispatch_item_id' => $item->id,
                    'returned_qty' => $ret,
                    'return_date' => $data['return_date'],
                    'remarks' => $data['remarks'] ?? null,
                ]);
            }

            $dispatch->recalcStatusAndTotals();
        });

        return back()->with('success','Return updated successfully');
    }
}
