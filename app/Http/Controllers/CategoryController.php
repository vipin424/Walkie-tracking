<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function index()
    {
        return view('categories.index');
    }

    public function getData(Request $request)
    {
        $query = Category::query()->select(['id', 'name', 'slug', 'is_featured', 'is_active'])->orderByDesc('id');

        return DataTables::of($query)
            ->editColumn('is_featured', function ($cat) {
                return $cat->is_featured ? '<span class="badge bg-warning text-dark"><i class="bi bi-star-fill"></i> Yes</span>' : '<span class="badge bg-secondary">No</span>';
            })
            ->editColumn('is_active', function ($cat) {
                return $cat->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
            })
            ->addColumn('actions', function ($cat) {
                return '
                    <a href="' . route('categories.edit', $cat) . '" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                    <form action="'.route('categories.destroy', $cat).'" method="POST" class="d-inline" onsubmit="return confirm(\'Delete this category?\');">
                        '.csrf_field().method_field('DELETE').'
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                ';
            })
            ->rawColumns(['is_featured', 'is_active', 'actions'])
            ->make(true);
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'badge_color' => 'nullable|string|max:50',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048'
        ]);

        $data['slug'] = Str::slug($data['name']);
        if(!isset($data['is_featured'])) $data['is_featured'] = 0;
        if(!isset($data['is_active'])) $data['is_active'] = 0;

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($data);
        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'badge_color' => 'nullable|string|max:50',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:2048'
        ]);

        $data['slug'] = Str::slug($data['name']);
        if(!isset($data['is_featured'])) $data['is_featured'] = 0;
        if(!isset($data['is_active'])) $data['is_active'] = 0;

        if ($request->hasFile('image')) {
            if ($category->image_path && \Storage::disk('public')->exists($category->image_path)) {
                \Storage::disk('public')->delete($category->image_path);
            }
            $data['image_path'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);
        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return back()->with('success', 'Category deleted.');
    }
}
