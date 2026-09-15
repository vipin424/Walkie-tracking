<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\WalkieInventory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class InventoryController extends Controller
{
    // ─── List ────────────────────────────────────────────────────────────────
    public function index()
    {
        $stats = [
            'total'       => WalkieInventory::count(),
            'available'   => WalkieInventory::where('status', 'available')->count(),
            'rented'      => WalkieInventory::where('status', 'rented')->count(),
            'maintenance' => WalkieInventory::where('status', 'maintenance')->count(),
        ];
        return view('inventory.index', compact('stats'));
    }

    // ─── DataTables AJAX ─────────────────────────────────────────────────────
    public function getData(Request $request)
    {
        $query = WalkieInventory::with('item')
            ->select('walkie_inventories.*')
            ->orderBy('id', 'asc');

        return DataTables::of($query)
            ->addColumn('item_name', fn($inv) => $inv->item?->name ?? '<span class="text-muted">—</span>')
            ->editColumn('status', fn($inv) => $inv->status_badge)
            ->editColumn('condition', fn($inv) => $inv->condition_badge)
            ->editColumn('purchase_date', fn($inv) => $inv->purchase_date?->format('d M Y') ?? '—')
            ->addColumn('actions', function ($inv) {
                return '
                    <a href="' . route('inventory.edit', $inv) . '" class="btn btn-sm btn-outline-primary me-1">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <button class="btn btn-sm btn-outline-danger delete-inv-btn" data-id="' . $inv->id . '">
                        <i class="bi bi-trash"></i>
                    </button>
                ';
            })
            ->rawColumns(['item_name', 'status', 'condition', 'actions'])
            ->make(true);
    }

    // ─── Scan Page ────────────────────────────────────────────────────────────
    public function scan()
    {
        $items = Item::where('is_active', 1)->orderBy('name')->get(['id', 'name', 'type']);
        return view('inventory.scan', compact('items'));
    }

    // ─── Store scanned serial (AJAX) ──────────────────────────────────────────
    public function storeScan(Request $request)
    {
        $request->validate([
            'serial_number' => 'required|string|max:255|unique:walkie_inventories,serial_number',
            'item_id'       => 'nullable|exists:items,id',
        ], [
            'serial_number.unique' => 'This serial number is already registered in inventory.',
        ]);

        $inv = WalkieInventory::create([
            'serial_number' => strtoupper(trim($request->serial_number)),
            'item_id'       => $request->item_id ?: null,
            'status'        => 'available',
            'condition'     => 'good',
        ]);

        return response()->json([
            'success'       => true,
            'message'       => 'Serial number registered successfully!',
            'serial_number' => $inv->serial_number,
            'id'            => $inv->id,
            'item_name'     => $inv->item?->name ?? '—',
            'created_at'    => $inv->created_at->format('d M Y, H:i'),
        ]);
    }

    // ─── Create (manual form) ─────────────────────────────────────────────────
    public function create()
    {
        $items = Item::where('is_active', 1)->orderBy('name')->get(['id', 'name', 'type']);
        return view('inventory.create', compact('items'));
    }

    // ─── Store (manual form) ──────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'serial_number' => 'required|string|max:255|unique:walkie_inventories,serial_number',
            'item_id'       => 'nullable|exists:items,id',
            'status'        => 'required|in:available,rented,maintenance,retired',
            'condition'     => 'required|in:good,fair,poor',
            'purchase_date' => 'nullable|date',
            'notes'         => 'nullable|string',
        ]);

        WalkieInventory::create([
            'serial_number' => strtoupper(trim($request->serial_number)),
            'item_id'       => $request->item_id ?: null,
            'status'        => $request->status,
            'condition'     => $request->condition,
            'purchase_date' => $request->purchase_date,
            'notes'         => $request->notes,
        ]);

        return redirect()->route('inventory.index')->with('success', 'Inventory item added successfully.');
    }

    // ─── Edit ─────────────────────────────────────────────────────────────────
    public function edit(WalkieInventory $inventory)
    {
        $items = Item::where('is_active', 1)->orderBy('name')->get(['id', 'name', 'type']);
        return view('inventory.edit', compact('inventory', 'items'));
    }

    // ─── Update ───────────────────────────────────────────────────────────────
    public function update(Request $request, WalkieInventory $inventory)
    {
        $request->validate([
            'serial_number' => 'required|string|max:255|unique:walkie_inventories,serial_number,' . $inventory->id,
            'item_id'       => 'nullable|exists:items,id',
            'status'        => 'required|in:available,rented,maintenance,retired',
            'condition'     => 'required|in:good,fair,poor',
            'purchase_date' => 'nullable|date',
            'notes'         => 'nullable|string',
        ]);

        $inventory->update([
            'serial_number' => strtoupper(trim($request->serial_number)),
            'item_id'       => $request->item_id ?: null,
            'status'        => $request->status,
            'condition'     => $request->condition,
            'purchase_date' => $request->purchase_date,
            'notes'         => $request->notes,
        ]);

        return redirect()->route('inventory.index')->with('success', 'Inventory item updated successfully.');
    }

    // ─── Destroy ──────────────────────────────────────────────────────────────
    public function destroy(WalkieInventory $inventory)
    {
        $inventory->delete();
        return response()->json(['success' => true]);
    }
}
