<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Services\Midtrans\CreateSnapTokenService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    // Show all user transactions (order history)
    public function index()
    {
        $transactions = Transaction::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Change the view to use the profile/pesanan template
        return view('pages.profile.pesanan', compact('transactions'));
    }
    
    // Show a specific transaction (order detail)
    public function show($id)
    {
        $order = Transaction::where('user_id', Auth::id())
            ->where('id', $id)
            ->with(['items.product', 'address', 'user'])
            ->firstOrFail();
        
        return view('pages.profile.orderdetail', compact('order'));
    }
    
    // Cancel an order
    public function cancelOrder(Request $request, $id)
    {
        $order = Transaction::where('user_id', Auth::id())
            ->where('id', $id)
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->firstOrFail();
        
        $order->status = 'cancelled';
        // Store cancellation reason in session for display purposes
        Session::flash('cancellation_reason', $request->cancellation_reason);
        $order->save();
        
        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Pesanan berhasil dibatalkan');
    }
    
    // Confirm order receipt
    public function confirmReceipt($id)
    {
        $order = Transaction::where('user_id', Auth::id())
            ->where('id', $id)
            ->where('status', 'shipped')
            ->firstOrFail();
        
        $order->status = 'delivered';
        $order->save();
        
        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Terima kasih telah mengkonfirmasi penerimaan pesanan.');
    }
    
    // Create a new transaction (place order)
    public function store(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'shipping_method' => 'required',
            'payment_method' => 'required',
        ]);
        
        // Get selected address
        $address = Address::where('user_id', Auth::id())
            ->where('id', $request->address_id)
            ->firstOrFail();
        
        // Get cart items
        $cartItems = CartItem::where('user_id', Auth::id())
            ->with('product')
            ->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda kosong');
        }
        
        // Calculate totals
        $subtotal = $cartItems->sum(function($item) {
            return $item->product->price * $item->quantity;
        });
        
        // Set shipping cost based on selected method
        $shippingCost = $request->shipping_method == 1 ? 10000 : 20000;
        
        // Apply any discount (from session, for example)
        $discount = 0;
        
        // Calculate total amount
        $totalAmount = $subtotal + $shippingCost - $discount;
        
        // Begin transaction
        DB::beginTransaction();
        
        try {
            // Create transaction record
            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'address_id' => $address->id,
                'order_number' => $this->generateOrderNumber(),
                'total_amount' => $totalAmount,
                'shipping_cost' => $shippingCost,
                'discount' => $discount,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'status' => 'pending',
                'payment_due_date' => Carbon::now()->addDay(), // 24 hours to pay
            ]);
            
            // Create transaction items
            foreach ($cartItems as $item) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price
                ]);
                
                // Update product stock
                $product = $item->product;
                $product->stock -= $item->quantity;
                $product->save();
            }
            
            // Clear the cart
            CartItem::where('user_id', Auth::id())->delete();
            
            // Generate payment token if using Midtrans
            if ($request->payment_method === 'midtrans') {
                // Generate snap token
                $midtrans = new CreateSnapTokenService($transaction);
                $snapToken = $midtrans->getSnapToken();
                
                $transaction->snap_token = $snapToken;
                $transaction->save();
            }
            
            DB::commit();
            
           // If payment is COD, redirect to success page
if ($request->payment_method === 'cod') {
    $transaction->payment_status = 'paid';
    $transaction->status = 'processing';
    $transaction->save();
    
    // Get recommended products for the success page
    $recommendedProducts = Product::inRandomOrder()->take(4)->get();
    
    // Change variable name from transaction to order
    $order = $transaction;
    
    return view('pages.order-success', compact('order', 'recommendedProducts'));
}
            
            // Otherwise redirect to payment page
            return redirect()->route('payment.show', $transaction->id);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memproses pesanan Anda: ' . $e->getMessage());
        }
    }
    
    // Show payment page
    public function showPayment($id)
    {
        $order = Transaction::where('user_id', Auth::id())
            ->where('id', $id)
            ->with(['user', 'address', 'items.product'])
            ->firstOrFail();
        
        $snapToken = $order->snap_token;
        
        return view('pages.payment', compact('order', 'snapToken'));
    }
    
    // Update payment status (callback from payment gateway)
    public function updatePayment(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);
        
        // Update transaction based on payment status
        if ($request->status === 'success') {
            $transaction->payment_status = 'paid';
            $transaction->status = 'processing';
            $transaction->save();
            
            return response()->json([
                'success' => true,
                'redirect_url' => route('orders.success', $transaction->id)
            ]);
        } elseif ($request->status === 'pending') {
            return response()->json([
                'success' => true,
                'redirect_url' => route('orders.pending', $transaction->id)
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran gagal. Silakan coba lagi.'
            ]);
        }
    }
    
    // Show order success page
    public function showSuccess($id)
    {
        $order = Transaction::where('user_id', Auth::id())
            ->where('id', $id)
            ->with(['items.product', 'address'])
            ->firstOrFail();
        
        // Calculate subtotal for the view
        $order->subtotal = $order->total_amount - $order->shipping_cost + $order->discount;
        
        // Get recommended products
        $recommendedProducts = Product::inRandomOrder()->take(4)->get();
        
        return view('pages.order-success', compact('order', 'recommendedProducts'));
    }
    
    // Show order pending page
    public function showPending($id)
    {
        $order = Transaction::where('user_id', Auth::id())
            ->where('id', $id)
            ->with(['items.product', 'address'])
            ->firstOrFail();
        
        // Calculate subtotal for the view
        $order->subtotal = $order->total_amount - $order->shipping_cost + $order->discount;
        
        return view('pages.order-pending', compact('order'));
    }
    
    // Handle storing a new address
    public function storeAddress(Request $request)
    {
        try {
            // Validate the address data
            $request->validate([
                'recipient_name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'full_address' => 'required|string',
                'province' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'postal_code' => 'required|string|max:20',
                'set_as_default' => 'sometimes|accepted',
            ]);
            
            // Create new address
            $address = new Address();
            $address->user_id = Auth::id();
            $address->recipient_name = $request->recipient_name;
            $address->phone = $request->phone;
            $address->full_address = $request->full_address;
            $address->province = $request->province;
            $address->city = $request->city;
            $address->postal_code = $request->postal_code;
            $address->is_default = $request->has('set_as_default') ? true : false;
            
            // If this is set as default, unset any previous default address
            if ($address->is_default) {
                Address::where('user_id', Auth::id())
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }
            
            $result = $address->save();
            
            if (!$result) {
                return redirect()->route('cart.checkout')->with('error', 'Gagal menyimpan alamat baru');
            }
            
            return redirect()->route('cart.checkout')->with('success', 'Alamat baru berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->route('cart.checkout')->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    // Generate a unique order number
    private function generateOrderNumber()
    {
        $prefix = 'IC';
        $date = Carbon::now()->format('Ymd');
        $random = Str::upper(Str::random(4));
        
        return $prefix . $date . $random;
    }

    /**
     * Handle payment gateway callback notification
     * This endpoint receives notifications from payment gateway (like Midtrans)
     */
    public function handlePaymentCallback(Request $request)
    {
        // Log the payment notification for debugging
        Log::info('Payment Callback received', $request->all());
        
        // Verify signature (for production)
        $serverKey = config('midtrans.server_key');
        $hashed = hash("sha512", $request->order_id.$request->status_code.$request->gross_amount.$serverKey);
        
        if ($hashed != $request->signature_key) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid signature'
            ], 403);
        }
        
        // Get necessary data from the request
        $orderId = $request->order_id;
        $transactionStatus = $request->transaction_status;
        $fraudStatus = $request->fraud_status;
        $paymentType = $request->payment_type;
        
        // Find the order by order number
        $transaction = Transaction::where('order_number', $orderId)->first();
        
        if (!$transaction) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found'
            ], 404);
        }
        
        // Update transaction based on payment status
        $transaction->payment_method = $paymentType;
        $transaction->midtrans_id = $request->transaction_id;
        
        // Handle various payment statuses
        if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
            // Payment is successful
            if ($fraudStatus == 'accept' || $fraudStatus == null) {
                $transaction->payment_status = 'paid';
                $transaction->status = 'processing';
            }
        } else if ($transactionStatus == 'pending') {
            // Payment is pending
            $transaction->payment_status = 'pending';
            $transaction->status = 'pending';
        } else if ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
            // Payment is failed
            $transaction->payment_status = 'failed';
            $transaction->status = 'cancelled';
        }
        
        $transaction->save();
        
        // Return appropriate response to payment gateway
        return response()->json(['status' => 'success']);
    }
}
