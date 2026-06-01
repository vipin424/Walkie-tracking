<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReturnItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderReturnController extends Controller
{
    /**
     * Record returned items for an order (partial or full)
     */
    public function store(Request $request, Order $order)
    {
        $request->validate([
            'order_item_id'  => 'required|exists:order_items,id',
            'returned_qty'   => 'required|integer|min:1',
            'return_date'    => 'required|date',
            'condition'      => 'required|in:good,damaged,missing',
            'notes'          => 'nullable|string|max:500',
        ]);

        $orderItem = OrderItem::findOrFail($request->order_item_id);

        // Ensure this item belongs to this order
        if ($orderItem->order_id !== $order->id) {
            return back()->with('error', 'Invalid item selected.');
        }

        // Calculate how many have already been returned for this item
        $alreadyReturned = OrderReturnItem::where('order_id', $order->id)
            ->where('order_item_id', $orderItem->id)
            ->sum('returned_qty');

        $stillPending = $orderItem->quantity - $alreadyReturned;

        if ($request->returned_qty > $stillPending) {
            return back()->with('error', "Only {$stillPending} unit(s) are pending for this item. You cannot return more than {$stillPending}.");
        }

        DB::transaction(function () use ($request, $order, $orderItem, $alreadyReturned) {

            // Save return entry
            OrderReturnItem::create([
                'order_id'      => $order->id,
                'order_item_id' => $orderItem->id,
                'returned_qty'  => $request->returned_qty,
                'return_date'   => $request->return_date,
                'condition'     => $request->condition,
                'notes'         => $request->notes,
                'recorded_by'   => Auth::id(),
            ]);

            // Recalculate overall order return_status
            $this->updateOrderReturnStatus($order);
        });

        return back()->with('success', 'Return recorded successfully!');
    }

    /**
     * Recalculate and update the order-level return_status
     */
    private function updateOrderReturnStatus(Order $order): void
    {
        $items = $order->items()->get();

        $totalDispatched = 0;
        $totalReturned   = 0;

        foreach ($items as $item) {
            $returned = OrderReturnItem::where('order_id', $order->id)
                ->where('order_item_id', $item->id)
                ->sum('returned_qty');

            $totalDispatched += $item->quantity;
            $totalReturned   += min($returned, $item->quantity);
        }

        if ($totalReturned <= 0) {
            $status = 'pending';
        } elseif ($totalReturned >= $totalDispatched) {
            $status = 'fully_returned';
        } else {
            $status = 'partial';
        }

        $order->update(['return_status' => $status]);
    }

    /**
     * Delete a return entry (undo)
     */
    public function destroy(Order $order, OrderReturnItem $returnItem)
    {
        if ($returnItem->order_id !== $order->id) {
            return back()->with('error', 'Invalid request.');
        }

        $returnItem->delete();

        // Recalculate status after deletion
        $this->updateOrderReturnStatus($order);

        return back()->with('success', 'Return entry deleted successfully.');
    }
}
