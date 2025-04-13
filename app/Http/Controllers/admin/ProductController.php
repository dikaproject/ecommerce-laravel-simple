<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');
        
        // Apply filters
        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category_id', $request->category);
        }
        
        if ($request->has('stock')) {
            if ($request->stock == 'in') {
                $query->where('stock', '>', 0);
            } elseif ($request->stock == 'low') {
                $query->where('stock', '>', 0)->where('stock', '<=', 10);
            } elseif ($request->stock == 'out') {
                $query->where('stock', 0);
            }
        }
        
        if ($request->has('sort')) {
            if ($request->sort == 'popular') {
                $query->leftJoin('transaction_items', 'products.id', '=', 'transaction_items.product_id')
                    ->select('products.*', DB::raw('COUNT(transaction_items.id) as sold'))
                    ->groupBy('products.id')
                    ->orderByDesc('sold');
            } elseif ($request->sort == 'price_asc') {
                $query->orderBy('price', 'asc');
            } elseif ($request->sort == 'price_desc') {
                $query->orderBy('price', 'desc');
            } elseif ($request->sort == 'newest') {
                $query->latest();
            }
        } else {
            $query->latest();
        }
        
        $products = $query->paginate(15);
        $categories = Category::all();
        
        return view('admin.products.index', compact('products', 'categories'));
    }
    
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'nullable',
        ]);
        
        $slug = Str::slug($request->name);
        $imagePath = $request->file('image')->store('products', 'public');
        
        Product::create([
            'name' => $request->name,
            'slug' => $slug,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'stock' => $request->stock,
            'description' => $request->description,
            'image' => 'storage/' . $imagePath,
            'rating' => 0,
        ]);
        
        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully');
    }
    
    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);
        
        // Get product statistics
        $product->sold = TransactionItem::where('product_id', $product->id)->sum('quantity');
        $product->views = 0; // Placeholder - would come from a view tracker model
        $product->reviews_count = 0; // Placeholder - would come from a reviews model
        $product->wishlist_count = 0; // Placeholder - would come from a wishlist model
        
        // Get product reviews
        $reviews = collect(); // Placeholder - would come from a reviews model
        
        return view('admin.products.show', compact('product', 'reviews'));
    }
    
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'nullable',
        ]);
        
        $product = Product::findOrFail($id);
        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->description = $request->description;
        
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image && Storage::exists('public/' . str_replace('storage/', '', $product->image))) {
                Storage::delete('public/' . str_replace('storage/', '', $product->image));
            }
            
            // Store new image
            $imagePath = $request->file('image')->store('products', 'public');
            $product->image = 'storage/' . $imagePath;
        }
        
        $product->save();
        
        return redirect()->route('admin.products.show', $product->id)
            ->with('success', 'Product updated successfully');
    }
    
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        // Check if product is used in any transactions
        $usedInTransactions = TransactionItem::where('product_id', $id)->exists();
        
        if ($usedInTransactions) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Cannot delete product because it is used in transactions');
        }
        
        // Delete product image
        if ($product->image && Storage::exists('public/' . str_replace('storage/', '', $product->image))) {
            Storage::delete('public/' . str_replace('storage/', '', $product->image));
        }
        
        $product->delete();
        
        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully');
    }
}
