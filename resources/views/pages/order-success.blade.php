<!-- resources/views/pages/order-success.blade.php -->
@extends('layouts.app')

@section('title', 'Pesanan Berhasil - Izzi Craft')

@section('styles')
<style>
    .success-header {
        background-color: #f8f9fa;
        padding: 40px 0 20px;
        margin-bottom: 30px;
    }
    
    .success-container {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .success-section {
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        padding: 30px;
        margin-bottom: 20px;
    }
    
    .success-icon {
        width: 80px;
        height: 80px;
        background-color: #d4edda;
        color: #28a745;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }
    
    .success-icon i {
        font-size: 2.5rem;
    }
    
    .success-title {
        font-size: 1.8rem;
        font-weight: 700;
        text-align: center;
        margin-bottom: 20px;
    }
    
    .success-message {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .order-info {
        background-color: #f8f9fa;
        border-radius: 5px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .order-info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    
    .order-info-label {
        font-weight: 600;
    }
    
    .order-timeline {
        display: flex;
        justify-content: space-between;
        margin: 30px 0;
        position: relative;
    }
    
    .order-timeline::before {
        content: '';
        position: absolute;
        top: 16px;
        left: 0;
        width: 100%;
        height: 2px;
        background-color: #dee2e6;
        z-index: 1;
    }
    
    .timeline-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        z-index: 2;
    }
    
    .timeline-icon {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background-color: #fff;
        border: 2px solid #dee2e6;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
    }
    
    .timeline-icon.active {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
    }
    
    .timeline-label {
        font-size: 0.8rem;
        text-align: center;
        max-width: 80px;
    }
    
    .product-item {
        display: flex;
        padding: 10px;
        border-bottom: 1px solid #dee2e6;
        margin-bottom: 10px;
    }
    
    .product-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 5px;
        margin-right: 10px;
    }
    
    .product-details {
        flex-grow: 1;
    }
    
    .product-name {
        font-weight: 500;
        margin-bottom: 5px;
    }
    
    .product-price {
        display: flex;
        justify-content: space-between;
    }
</style>
@endsection

@section('content')
<!-- Success Header -->
<div class="success-header">
    <div class="container">
        <h1 class="fw-bold">Pesanan Berhasil</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pesanan Berhasil</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <div class="success-container">
        <!-- Success Message -->
        <div class="success-section">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            <h2 class="success-title">Terima Kasih atas Pesanan Anda!</h2>
            <div class="success-message">
                <p>Pesanan Anda telah berhasil kami terima dan sedang diproses.</p>
                <p>Detail pesanan telah dikirim ke email Anda.</p>
            </div>
            
            <!-- Order Information -->
            <div class="order-info">
                <h5 class="mb-3">Informasi Pesanan</h5>
                <div class="order-info-row">
                    <span class="order-info-label">Nomor Pesanan:</span>
                    <span>{{ $order->order_number }}</span>
                </div>
                <div class="order-info-row">
                    <span class="order-info-label">Tanggal Pemesanan:</span>
                    <span>{{ $order->created_at->format('d M Y, H:i') }}</span>
                </div>
                <div class="order-info-row">
                    <span class="order-info-label">Status Pesanan:</span>
                    <span class="badge bg-success">Berhasil</span>
                </div>
                <div class="order-info-row">
                    <span class="order-info-label">Metode Pembayaran:</span>
                    <span>{{ $order->payment_method }}</span>
                </div>
                <div class="order-info-row">
                    <span class="order-info-label">Total Pembayaran:</span>
                    <span class="fw-bold">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
            
            <!-- Order Timeline -->
            <div>
                <h5 class="mb-3">Status Pesanan</h5>
                <div class="order-timeline">
                    <div class="timeline-step">
                        <div class="timeline-icon active">
                            <i class="fas fa-check small"></i>
                        </div>
                        <div class="timeline-label">Pesanan Dibuat</div>
                    </div>
                    <div class="timeline-step">
                        <div class="timeline-icon active">
                            <i class="fas fa-check small"></i>
                        </div>
                        <div class="timeline-label">Pembayaran Berhasil</div>
                    </div>
                    <div class="timeline-step">
                        <div class="timeline-icon">
                            <i class="fas fa-box small"></i>
                        </div>
                        <div class="timeline-label">Pesanan Diproses</div>
                    </div>
                    <div class="timeline-step">
                        <div class="timeline-icon">
                            <i class="fas fa-shipping-fast small"></i>
                        </div>
                        <div class="timeline-label">Pesanan Dikirim</div>
                    </div>
                    <div class="timeline-step">
                        <div class="timeline-icon">
                            <i class="fas fa-home small"></i>
                        </div>
                        <div class="timeline-label">Pesanan Diterima</div>
                    </div>
                </div>
            </div>
            
            <!-- Order Items -->
            <div>
                <h5 class="mb-3">Produk yang Dibeli</h5>
                <div>
                    @foreach($order->items as $item)
                    <div class="product-item">
                        <img src="{{ asset($item->product->image) }}" class="product-image" alt="{{ $item->product->name }}">
                        <div class="product-details">
                            <div class="product-name">{{ $item->product->name }}</div>
                            <div class="product-price">
                                <span>{{ $item->quantity }} x Rp{{ number_format($item->price, 0, ',', '.') }}</span>
                                <span>Rp{{ number_format($item->quantity * $item->price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Order Summary -->
                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Subtotal</span>
                        <span>Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Pengiriman</span>
                        <span>Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    @if($order->discount > 0)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Diskon</span>
                        <span>-Rp{{ number_format($order->discount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <hr>
                    <div class="d-flex justify-content-between align-items-center fw-bold">
                        <span>Total</span>
                        <span>Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
                
                <!-- Shipping Address -->
                <div class="mt-4">
                    <h5 class="mb-3">Alamat Pengiriman</h5>
                    <div class="p-3 bg-light rounded">
                        <div class="fw-bold">{{ $order->address->recipient_name }}</div>
                        <div>{{ $order->address->phone }}</div>
                        <div>{{ $order->address->full_address }}</div>
                        <div>{{ $order->address->city }}, {{ $order->address->province }}, {{ $order->address->postal_code }}</div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="d-grid gap-2 mt-4">
                <a href="{{ route('orders.index') }}" class="btn btn-danger">
                    <i class="fas fa-clipboard-list me-1"></i> Lihat Semua Pesanan
                </a>
                <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-home me-1"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
        
        <!-- Recommended Products -->
        <div class="success-section">
            <h5 class="mb-3">Rekomendasi Produk untuk Anda</h5>
            <div class="row row-cols-2 row-cols-md-4 g-3">
                @foreach($recommendedProducts as $product)
                <div class="col">
                    <div class="card h-100 border">
                        <a href="{{ route('products.show', $product->id) }}" class="text-decoration-none">
                            <img src="{{ asset($product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                            <div class="card-body">
                                <h6 class="card-title text-dark">{{ $product->name }}</h6>
                                <p class="card-text fw-bold text-danger">Rp{{ number_format($product->price, 0, ',', '.') }}</p>
                            </div>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection