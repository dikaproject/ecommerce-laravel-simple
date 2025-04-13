<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of all products with filtering
     */
    public function index(Request $request)
    {
        // Start with base query
        $productsQuery = Product::query();
        
        // Apply category filter
        if ($request->has('categories') && !empty($request->categories)) {
            $productsQuery->whereIn('category_id', $request->categories);
        }
        
        // Apply price filter
        if ($request->filled('min_price')) {
            $productsQuery->where('price', '>=', $request->min_price);
        }
        
        if ($request->filled('max_price')) {
            $productsQuery->where('price', '<=', $request->max_price);
        }
        
        // Apply rating filter
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
                $productsQuery->latest(); // Default sorting
                break;
        }
        
        // Paginate results
        $products = $productsQuery->paginate(12)->withQueryString();
        
        // Get all categories for the filter sidebar
        $categories = Category::all();
        
        return view('pages.products.index', compact('products', 'categories'));
    }
    
    /**
     * Display the specified product
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);
        
        // Calculate total sold quantity for this product
        $soldQuantity = TransactionItem::where('product_id', $product->id)
            ->sum('quantity');
        
        $product->sold = $soldQuantity;
        
        return view('pages.products.show', compact('product'));
    }
}