<?php

namespace App\Services;

use App\Models\Item;
use App\Models\InventoryReservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Check if the requested quantity of an item is available for the given event dates.
     *
     * @param int $itemId
     * @param string $eventStart (Y-m-d)
     * @param string $eventEnd (Y-m-d)
     * @param int $requestedQty
     * @return array ['available' => bool, 'available_qty' => int, 'locked_from' => string, 'locked_until' => string]
     */
    public function checkAvailability(int $itemId, string $eventStart, string $eventEnd, int $requestedQty = 1): array
    {
        $item = Item::findOrFail($itemId);

        // 1. Calculate true lock dates including item-specific buffers
        $lockedFrom = Carbon::parse($eventStart)->subDays($item->buffer_days_before)->format('Y-m-d');
        $lockedUntil = Carbon::parse($eventEnd)->addDays($item->buffer_days_after)->format('Y-m-d');

        // 2. Find maximum concurrent reservations in this period
        // We look for any reservation that overlaps with [lockedFrom, lockedUntil]
        // An overlap occurs if (Reservation.Start <= Requested.End AND Reservation.End >= Requested.Start)
        $reservedQty = InventoryReservation::where('item_id', $itemId)
            ->where('locked_from', '<=', $lockedUntil)
            ->where('locked_until', '>=', $lockedFrom)
            ->sum('quantity_reserved');

        $availableQty = $item->total_stock - $reservedQty;

        return [
            'is_available' => $availableQty >= $requestedQty,
            'available_qty' => max(0, $availableQty),
            'total_stock' => $item->total_stock,
            'locked_from' => $lockedFrom,
            'locked_until' => $lockedUntil,
            'requested_qty' => $requestedQty
        ];
    }

    /**
     * Reserve inventory for an order
     *
     * @param int $orderId
     * @param int $itemId
     * @param string $eventStart
     * @param string $eventEnd
     * @param int $quantity
     * @return InventoryReservation
     * @throws \Exception
     */
    public function reserveInventory(int $orderId, int $itemId, string $eventStart, string $eventEnd, int $quantity)
    {
        // Double check availability before saving to prevent race conditions
        $availability = $this->checkAvailability($itemId, $eventStart, $eventEnd, $quantity);

        if (!$availability['is_available']) {
            throw new \Exception("Not enough inventory available for the selected dates.");
        }

        return InventoryReservation::create([
            'item_id' => $itemId,
            'order_id' => $orderId,
            'quantity_reserved' => $quantity,
            'locked_from' => $availability['locked_from'],
            'locked_until' => $availability['locked_until'],
            'reason' => 'customer_booking'
        ]);
    }
}
