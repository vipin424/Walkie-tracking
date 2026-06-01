<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReturnItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

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

        try {
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

            DB::transaction(function () use ($request, $order, $orderItem) {

                OrderReturnItem::create([
                    'order_id'      => $order->id,
                    'order_item_id' => $orderItem->id,
                    'returned_qty'  => (int) $request->returned_qty,
                    'return_date'   => $request->return_date,
                    'condition'     => $request->condition,
                    'notes'         => $request->notes,
                    'recorded_by'   => Auth::id(),
                ]);

                $this->updateOrderReturnStatus($order);
            });

            return back()->with('success', 'Return recorded successfully!');

        } catch (Throwable $e) {
            Log::error('OrderReturn store failed', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
                'trace'    => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Failed to record return: ' . $e->getMessage());
        }
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
        try {
            if ($returnItem->order_id !== $order->id) {
                return back()->with('error', 'Invalid request.');
            }

            $returnItem->delete();
            $this->updateOrderReturnStatus($order);

            return back()->with('success', 'Return entry deleted successfully.');

        } catch (Throwable $e) {
            Log::error('OrderReturn destroy failed', [
                'order_id'      => $order->id,
                'return_item_id'=> $returnItem->id,
                'error'         => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to delete return entry: ' . $e->getMessage());
        }
    }
}
