<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    // Display cart items
    public function index()
    {
        $cartItems = CartItem::where('user_id', Auth::id())
            ->with('product')
            ->get();
        
        // Debug log cart items
        Log::info('Cart Items for user ' . Auth::id(), ['items' => $cartItems->toArray()]);
        
        // Calculate cart totals
        $subtotal = $cartItems->sum(function($item) {
            return $item->product->price * $item->quantity;
        });
        
        $discount = 0; // You can implement discounts later
        $total = $subtotal - $discount;
        
        // Get recommended products
        $recommendedProducts = Product::inRandomOrder()->take(4)->get();
        
        // Add 'sold' property to products for the view
        foreach ($recommendedProducts as $product) {
            $product->sold = rand(10, 100); // Placeholder, replace with actual sold quantity calculation
        }
        
        return view('pages.cart.index', compact('cartItems', 'subtotal', 'discount', 'total', 'recommendedProducts'));
    }
    
    // Add item to cart
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);
        
        $product = Product::findOrFail($request->product_id);
        $quantity = (int)$request->quantity; // Force integer conversion
        
        // Log the add to cart request
        Log::info('Adding to cart', [
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'requested_quantity' => $quantity
        ]);
        
        // Check if product has enough stock
        if ($product->stock < $quantity) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok produk tidak cukup'
                ]);
            }
            return back()->with('error', 'Stok produk tidak cukup');
        }
        
        // Check if item already in cart
        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->first();
        
        if ($cartItem) {
            // Update quantity if item already in cart
            $newQuantity = $cartItem->quantity + $quantity;
            
            // Make sure we don't exceed available stock
            if ($newQuantity > $product->stock) {
                $newQuantity = $product->stock;
            }
            
            $cartItem->update(['quantity' => $newQuantity]);
            Log::info('Updated cart item quantity', ['item_id' => $cartItem->id, 'new_quantity' => $newQuantity]);
        } else {
            // Add new item to cart
            $cartItem = CartItem::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'quantity' => $quantity
            ]);
            Log::info('Created new cart item', ['item_id' => $cartItem->id, 'quantity' => $quantity]);
        }
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke keranjang',
                'quantity' => $cartItem->quantity // Include quantity in response
            ]);
        }
        
        return redirect()->route('cart.index')->with('success', 'Produk berhasil ditambahkan ke keranjang');
    }
    
    // Update cart item quantity
    public function update(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:cart_items,id',
            'quantity' => 'required|integer|min:1'
        ]);
        
        $cartItem = CartItem::where('id', $request->item_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        
        $product = $cartItem->product;
        
        // Make sure we don't exceed available stock
        if ($request->quantity > $product->stock) {
            $cartItem->update(['quantity' => $product->stock]);
        } else {
            $cartItem->update(['quantity' => $request->quantity]);
        }
        
        // Recalculate cart totals
        $cartItems = CartItem::where('user_id', Auth::id())->with('product')->get();
        $subtotal = $cartItems->sum(function($item) {
            return $item->product->price * $item->quantity;
        });
        $discount = 0;
        $total = $subtotal - $discount;
        
        return response()->json([
            'success' => true,
            'item_price' => $product->price,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total
        ]);
    }
    
    // Remove item from cart
    public function remove(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:cart_items,id'
        ]);
        
        $cartItem = CartItem::where('id', $request->item_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        
        $cartItem->delete();
        
        // Recalculate cart totals
        $cartItems = CartItem::where('user_id', Auth::id())->with('product')->get();
        $subtotal = $cartItems->sum(function($item) {
            return $item->product->price * $item->quantity;
        });
        $discount = 0;
        $total = $subtotal - $discount;
        
        return response()->json([
            'success' => true,
            'count' => $cartItems->count(),
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total
        ]);
    }
    
    // Remove multiple items from cart
    public function removeSelected(Request $request)
    {
        $request->validate([
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:cart_items,id'
        ]);
        
        CartItem::whereIn('id', $request->item_ids)
            ->where('user_id', Auth::id())
            ->delete();
        
        // Recalculate cart totals
        $cartItems = CartItem::where('user_id', Auth::id())->with('product')->get();
        $subtotal = $cartItems->sum(function($item) {
            return $item->product->price * $item->quantity;
        });
        $discount = 0;
        $total = $subtotal - $discount;
        
        return response()->json([
            'success' => true,
            'count' => $cartItems->count(),
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total
        ]);
    }
    
    // Show checkout page
    public function checkout()
    {
        $cartItems = CartItem::where('user_id', Auth::id())
            ->with('product')
            ->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda kosong');
        }
        
        // Calculate cart totals
        $subtotal = $cartItems->sum(function($item) {
            return $item->product->price * $item->quantity;
        });
        
        $discount = 0;
        $shipping = 10000; // Default shipping cost
        $total = $subtotal - $discount + $shipping;
        
        // Get user addresses
        $addresses = Auth::user()->addresses;
        
        // Mock shipping methods for the example
        $shippingMethods = [
            (object)[
                'id' => 1,
                'name' => 'Regular Delivery',
                'price' => 10000,
                'estimated_delivery' => '3-5 hari'
            ],
            (object)[
                'id' => 2,
                'name' => 'Express Delivery',
                'price' => 20000,
                'estimated_delivery' => '1-2 hari'
            ]
        ];
        
        // Mock payment methods for the example
        $paymentMethods = [
            (object)[
                'id' => 'midtrans',
                'name' => 'Midtrans (Credit Card, VA, E-Wallet)',
                'logo' => 'images/midtrans-logo.png',
                'description' => 'Bayar dengan kartu kredit, virtual account, atau e-wallet'
            ],
            (object)[
                'id' => 'cod',
                'name' => 'Cash on Delivery',
                'logo' => null,
                'description' => 'Bayar saat barang diterima'
            ]
        ];
        
        // Mock provinces for address form
        $provinces = [
            (object)['id' => 1, 'name' => 'DKI Jakarta'],
            (object)['id' => 2, 'name' => 'Jawa Barat'],
            (object)['id' => 3, 'name' => 'Jawa Tengah'],
            (object)['id' => 4, 'name' => 'Jawa Timur'],
            (object)['id' => 5, 'name' => 'Bali'],
        ];
        
        return view('pages.cart.checkout', compact(
            'cartItems', 'subtotal', 'discount', 'shipping', 'total',
            'addresses', 'shippingMethods', 'paymentMethods', 'provinces'
        ));
    }
    
    // Apply promo code
    public function applyPromo(Request $request)
    {
        $request->validate([
            'promo_code' => 'required|string'
        ]);
        
        // Simple mock promo code handling
        $promoCode = strtoupper($request->promo_code);
        
        if ($promoCode === 'WELCOME10') {
            $discount = 10000;
            return response()->json([
                'success' => true,
                'discount' => $discount
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Kode promo tidak valid atau sudah kadaluarsa'
        ]);
    }
}
