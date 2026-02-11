<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ItemController extends Controller
{
    public function index()
    {
        return view('items.index');
    }

    public function getData(Request $request)
    {
        $query = Item::query()->select(['id', 'name', 'type', 'description', 'unit_price', 'tax_percent', 'is_active'])->orderByDesc('id');

        return DataTables::of($query)
            ->editColumn('type', function ($item) {
                return $item->type ?? '-';
            })
            ->editColumn('description', function ($item) {
                return $item->description ? \Str::limit($item->description, 50) : '-';
            })
            ->editColumn('unit_price', function ($item) {
                return '₹' . number_format($item->unit_price, 2);
            })
            ->editColumn('tax_percent', function ($item) {
                return $item->tax_percent . '%';
            })
            ->editColumn('is_active', function ($item) {
                return $item->is_active 
                    ? '<span class="badge bg-success-subtle text-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>';
            })
            ->addColumn('actions', function ($item) {
                return '
                    <a href="' . route('items.edit', $item) . '" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <button class="btn btn-sm btn-outline-danger delete-btn" data-id="' . $item->id . '">
                        <i class="bi bi-trash"></i>
                    </button>
                ';
            })
            ->rawColumns(['is_active', 'actions'])
            ->make(true);
    }

    public function create()
    {
        return view('items.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'tax_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        Item::create($request->all());

        return redirect()->route('items.index')->with('success', 'Item created successfully.');
    }

    public function edit(Item $item)
    {
        return view('items.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'tax_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        $item->update($request->all());

        return redirect()->route('items.index')->with('success', 'Item updated successfully.');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return response()->json(['success' => true]);
    }

    // Autocomplete API
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        $items = Item::where('is_active', 1)
            ->where('name', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get(['id', 'name', 'type', 'description', 'unit_price', 'tax_percent']);

        return response()->json($items);
    }
}
