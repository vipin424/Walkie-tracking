<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Top Categories (Small Carousel, not featured)
        $topCategories = Category::where('is_active', 1)->where('is_featured', 0)->get();
        
        // 2. Featured Broad Categories (Large cards)
        $featuredCategories = Category::where('is_active', 1)->where('is_featured', 1)->get();
        
        $trendingItems = Item::where('is_active', 1)->latest()->take(10)->get();

        return view('storefront.home', compact('topCategories', 'featuredCategories', 'trendingItems'));
    }

    public function category($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $items = Item::where('category_id', $category->id)->where('is_active', 1)->get();

        return view('storefront.category', compact('category', 'items'));
    }

    public function product($category_slug, $item_slug)
    {
        $item = Item::where('slug', $item_slug)->firstOrFail();
        
        return view('storefront.product', compact('item'));
    }
}
