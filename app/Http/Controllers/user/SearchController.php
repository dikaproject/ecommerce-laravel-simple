<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('search');
        
        // Search products by name or description
        $products = Product::where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->paginate(12);
        
        // Get categories for filter sidebar
        $categories = Category::all();
        
        return view('pages.products.search', compact('products', 'categories', 'query'));
    }
}
