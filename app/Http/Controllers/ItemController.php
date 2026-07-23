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
        $query = Item::query()->select(['id', 'name', 'type', 'description', 'unit_price', 'total_stock', 'is_active', 'image_path'])->orderByDesc('id');

        return DataTables::of($query)
            ->editColumn('image', function ($item) {
                if ($item->image_path) {
                    $url = \Str::startsWith($item->image_path, 'http') ? $item->image_path : asset('storage/' . $item->image_path);
                    return '<img src="' . $url . '" width="40" height="40" style="object-fit:cover; border-radius:4px;">';
                }
                return '<div style="width:40px;height:40px;background:#eee;border-radius:4px;"></div>';
            })
            ->editColumn('type', function ($item) {
                return $item->type ?? '-';
            })
            ->editColumn('total_stock', function ($item) {
                return $item->total_stock ?? 0;
            })
            ->editColumn('unit_price', function ($item) {
                return '₹' . number_format($item->unit_price, 2);
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
            ->rawColumns(['image', 'is_active', 'actions'])
            ->make(true);
    }

    public function create()
    {
        $categories = \App\Models\Category::all();
        return view('items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'type' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'security_deposit' => 'nullable|numeric|min:0',
            'tax_percent' => 'nullable|numeric|min:0|max:100',
            'total_stock' => 'nullable|integer|min:0',
            'buffer_days_before' => 'nullable|integer|min:0',
            'buffer_days_after' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:2048'
        ]);

        $data = $request->except('image');
        $data['slug'] = \Str::slug($request->name);
        
        if (!isset($data['is_active'])) {
            $data['is_active'] = 0;
        }

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('items', 'public');
        }

        Item::create($data);

        return redirect()->route('items.index')->with('success', 'Item created successfully.');
    }

    public function edit(Item $item)
    {
        $categories = \App\Models\Category::all();
        return view('items.edit', compact('item', 'categories'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'type' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'security_deposit' => 'nullable|numeric|min:0',
            'tax_percent' => 'nullable|numeric|min:0|max:100',
            'total_stock' => 'nullable|integer|min:0',
            'buffer_days_before' => 'nullable|integer|min:0',
            'buffer_days_after' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:2048'
        ]);

        $data = $request->except('image');
        $data['slug'] = \Str::slug($request->name);
        
        if (!isset($data['is_active'])) {
            $data['is_active'] = 0;
        }

        if ($request->hasFile('image')) {
            if ($item->image_path && \Storage::disk('public')->exists($item->image_path)) {
                \Storage::disk('public')->delete($item->image_path);
            }
            $data['image_path'] = $request->file('image')->store('items', 'public');
        }

        $item->update($data);

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
