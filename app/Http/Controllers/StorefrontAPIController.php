<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\InventoryService;

class StorefrontAPIController extends Controller
{
    public function checkAvailability(Request $request, InventoryService $inventoryService)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'qty' => 'integer|min:1'
        ]);

        $qty = $request->input('qty', 1);

        try {
            $availability = $inventoryService->checkAvailability(
                $request->item_id,
                $request->start_date,
                $request->end_date,
                $qty
            );
            
            return response()->json([
                'success' => true,
                'data' => $availability
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
