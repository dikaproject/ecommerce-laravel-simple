<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of all categories
     */
    public function index()
    {
        $categories = Category::withCount('products')->get();
        
        return view('pages.category.index', compact('categories'));
    }

    /**
     * Display products for a specific category
     */
    public function show(Request $request, $slug)
    {
        // Find the category by slug
        $category = Category::where('slug', $slug)->firstOrFail();
        
        // Get products query
        $productsQuery = Product::where('category_id', $category->id);
        
        // Apply price filter if provided
        if ($request->filled('min_price')) {
            $productsQuery->where('price', '>=', $request->min_price);
        }
        
        if ($request->filled('max_price')) {
            $productsQuery->where('price', '<=', $request->max_price);
        }
        
        // Apply rating filter if provided
        if ($request->has('ratings') && !empty($request->ratings)) {
            $productsQuery->whereIn('rating', $request->ratings);
        }
        
        // Apply sorting
        switch($request->sort) {
            case 'price-low':
                $productsQuery->orderBy('price', 'asc');
                break;
            case 'price-high':
                $productsQuery->orderBy('price', 'desc');
                break;
            case 'popular':
                // Calculate sold quantities from transaction_items
                $productsQuery->select('products.*')
                    ->leftJoin('transaction_items', 'products.id', '=', 'transaction_items.product_id')
                    ->groupBy('products.id')
                    ->orderByRaw('COALESCE(SUM(transaction_items.quantity), 0) DESC');
                break;
            case 'rating':
                $productsQuery->orderBy('rating', 'desc');
                break;
            default:
                $productsQuery->latest(); // Default: newest products first
                break;
        }
        
        // Paginate the products
        $products = $productsQuery->paginate(12)->withQueryString();
        
        // Get other categories for the "Other Categories" section
        $otherCategories = Category::withCount('products')
                            ->where('id', '!=', $category->id)
                            ->limit(4)
                            ->get();
        
        return view('pages.category.show', compact('category', 'products', 'otherCategories'));
    }
}