<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\user\CartController;
use App\Http\Controllers\user\CategoryController;
use App\Http\Controllers\user\ProductController;
use App\Http\Controllers\user\ProfileController;
use App\Http\Controllers\user\SearchController;
use App\Http\Controllers\user\TransactionController;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    $categories = Category::take(4)->get();

    // Get new products for the new products section
    $newProducts = Product::latest()->take(4)->get();
    
    // Get popular products based on transaction quantities
    $popularProducts = Product::select('products.*')
        ->leftJoin('transaction_items', 'products.id', '=', 'transaction_items.product_id')
        ->groupBy('products.id')
        ->orderByRaw('COALESCE(SUM(transaction_items.quantity), 0) DESC')
        ->take(4)
        ->get();
    
    return view('pages.home', compact('categories', 'newProducts', 'popularProducts'));
})->name('home');

// Category routes
Route::get('/categories', [CategoryController::class, 'index'])->name('category.index');
Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('category.show');

// Product routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
Route::get('/search', [SearchController::class, 'search'])->name('products.search');

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    
    // Password reset
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
});

Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Cart routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/remove-selected', [CartController::class, 'removeSelected'])->name('cart.remove-selected');
    Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    
    // Address routes
    Route::post('/addresses/store', [TransactionController::class, 'storeAddress'])->name('addresses.store');
    
    // Transaction/Orders routes
    Route::get('/orders', [TransactionController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [TransactionController::class, 'show'])->name('orders.show');
    Route::post('/orders', [TransactionController::class, 'store'])->name('orders.store');
    Route::put('/orders/{id}/cancel', [TransactionController::class, 'cancelOrder'])->name('orders.cancel');
    Route::put('/orders/{id}/confirm', [TransactionController::class, 'confirmReceipt'])->name('orders.confirm');
    Route::get('/payment/{id}', [TransactionController::class, 'showPayment'])->name('payment.show');
    Route::post('/payment/{id}/update', [TransactionController::class, 'updatePayment'])->name('payments.update');
    Route::get('/orders/{id}/success', [TransactionController::class, 'showSuccess'])->name('orders.success');
    Route::get('/orders/{id}/pending', [TransactionController::class, 'showPending'])->name('orders.pending');
});

// Payment gateway callback - public route, no auth required
Route::post('/payment/callback', [TransactionController::class, 'handlePaymentCallback'])
    ->name('payment.callback')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [App\Http\Controllers\admin\DashboardController::class, 'index'])->name('dashboard');
    
    // Products management
    Route::resource('products', App\Http\Controllers\admin\ProductController::class);
    
    // Categories management
    Route::resource('categories', App\Http\Controllers\admin\CategoryController::class);
    
    // Orders management
    Route::get('orders', [App\Http\Controllers\admin\TransactionController::class, 'index'])->name('orders.index');
    Route::get('orders/{id}', [App\Http\Controllers\admin\TransactionController::class, 'show'])->name('orders.show');
    Route::get('orders/{id}/edit', [App\Http\Controllers\admin\TransactionController::class, 'edit'])->name('orders.edit');
    Route::put('orders/{id}', [App\Http\Controllers\admin\TransactionController::class, 'update'])->name('orders.update');
    Route::put('orders/{id}/status', [App\Http\Controllers\admin\TransactionController::class, 'updateStatus'])->name('orders.status.update');
    
    // Customers management
    Route::get('customers', [App\Http\Controllers\admin\UserController::class, 'index'])->name('customers.index');
    Route::get('customers/create', [App\Http\Controllers\admin\UserController::class, 'create'])->name('customers.create');
    Route::post('customers', [App\Http\Controllers\admin\UserController::class, 'store'])->name('customers.store');
    Route::get('customers/{id}', [App\Http\Controllers\admin\UserController::class, 'show'])->name('customers.show');
    Route::get('customers/{id}/edit', [App\Http\Controllers\admin\UserController::class, 'edit'])->name('customers.edit');
    Route::put('customers/{id}', [App\Http\Controllers\admin\UserController::class, 'update'])->name('customers.update');
    Route::delete('customers/{id}', [App\Http\Controllers\admin\UserController::class, 'destroy'])->name('customers.destroy');

    // Profile and Settings
    Route::get('profile', [App\Http\Controllers\admin\ProfileController::class, 'index'])->name('profile');
    Route::put('profile/update', [App\Http\Controllers\admin\ProfileController::class, 'update'])->name('profile.update');
    
    // Sales reports
    Route::get('sales/data', [App\Http\Controllers\admin\DashboardController::class, 'salesData'])->name('sales.data');

    // Analytics routes
    Route::get('analytics', [App\Http\Controllers\admin\AnalyticsController::class, 'index'])->name('analytics');
    Route::get('analytics/sales-data', [App\Http\Controllers\admin\AnalyticsController::class, 'salesData'])->name('analytics.sales-data');
    Route::get('analytics/export', [App\Http\Controllers\admin\AnalyticsController::class, 'export'])->name('analytics.export');

    Route::get('transactions', [App\Http\Controllers\admin\TransactionController::class, 'index'])->name('transactions.index');
    Route::get('transactions/{id}', [App\Http\Controllers\admin\TransactionController::class, 'show'])->name('transactions.show');
    Route::put('transactions/{id}/confirm', [App\Http\Controllers\admin\TransactionController::class, 'confirm'])->name('transactions.confirm');
    Route::get('transactions/export', [App\Http\Controllers\admin\TransactionController::class, 'export'])->name('transactions.export');
});

// Static pages
Route::get('/terms', function() {
    return view('pages.static.terms');
})->name('terms');

Route::get('/privacy', function() {
    return view('pages.static.privacy');
})->name('privacy');

// User profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    
    Route::get('/user-orders', [ProfileController::class, 'orders'])->name('user.orders'); // Renamed to avoid conflict
});