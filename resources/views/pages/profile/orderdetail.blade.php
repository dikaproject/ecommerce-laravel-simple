@extends('layouts.app')

@section('title', 'Detail Pesanan - Izzi Craft')

@section('styles')
<style>
    .order-header {
        background-color: #f8f9fa;
        padding: 40px 0 20px;
        margin-bottom: 30px;
    }
    
    .order-container {
        max-width: 900px;
        margin: 0 auto;
    }
    
    .order-section {
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
    }
    
    .section-title i {
        margin-right: 8px;
        color: #dc3545;
    }
    
    .order-info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    
    .order-info-label {
        font-weight: 600;
        color: #6c757d;
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
        padding: 15px;
        border-bottom: 1px solid #dee2e6;
    }
    
    .product-image {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 5px;
        margin-right: 15px;
    }
    
    .product-details {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    
    .product-name {
        font-weight: 500;
        margin-bottom: 5px;
    }
    
    .product-meta {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }
    
    .address-card {
        background-color: #f8f9fa;
        border-radius: 5px;
        padding: 15px;
    }
    
    .status-badge {
        padding: 8px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .action-btn {
        min-width: 120px;
    }
</style>
@endsection

@section('content')
<!-- Order Header -->
<div class="order-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="fw-bold">Detail Pesanan</h1>
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('orders.index') }}" class="text-decoration-none">Pesanan Saya</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail Pesanan</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <div class="order-container">
        <!-- Order Status & Actions -->
        <div class="order-section">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        @php
                            $statusClass = 'secondary';
                            switch($order->status) {
                                case 'pending':
                                    $statusClass = 'warning';
                                    $statusText = 'Menunggu Pembayaran';
                                    break;
                                case 'processing':
                                    $statusClass = 'info';
                                    $statusText = 'Sedang Diproses';
                                    break;
                                case 'shipped':
                                    $statusClass = 'primary';
                                    $statusText = 'Dalam Pengiriman';
                                    break;
                                case 'delivered':
                                    $statusClass = 'success';
                                    $statusText = 'Telah Diterima';
                                    break;
                                case 'cancelled':
                                    $statusClass = 'danger';
                                    $statusText = 'Dibatalkan';
                                    break;
                                default:
                                    $statusText = ucfirst($order->status);
                            }
                        @endphp
                        <span class="status-badge bg-{{ $statusClass }} text-white me-2">
                            <i class="fas fa-circle me-1 small"></i> {{ $statusText }}
                        </span>
                        <span class="text-muted">{{ $order->created_at->format('d M Y, H:i') }}</span>
                    </div>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    @if($order->status === 'pending' && $order->payment_status === 'pending')
                        <a href="{{ route('payment.show', $order->id) }}" class="btn btn-danger action-btn me-2">
                            <i class="fas fa-credit-card me-1"></i> Bayar
                        </a>
                    @elseif($order->status === 'shipped')
                        <button type="button" class="btn btn-success action-btn me-2" data-bs-toggle="modal" data-bs-target="#confirmReceiptModal">
                            <i class="fas fa-check me-1"></i> Diterima
                        </button>
                    @endif
                    
                    @if($order->status !== 'cancelled' && $order->status !== 'delivered')
                        <button type="button" class="btn btn-outline-danger action-btn" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">
                            <i class="fas fa-times me-1"></i> Batalkan
                        </button>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Order Progress -->
        <div class="order-section">
            <div class="section-title">
                <i class="fas fa-tasks"></i> Progress Pesanan
            </div>
            
            <div class="order-timeline">
                <div class="timeline-step">
                    <div class="timeline-icon active">
                        <i class="fas fa-check small"></i>
                    </div>
                    <div class="timeline-label">Pesanan Dibuat</div>
                </div>
                <div class="timeline-step">
                    <div class="timeline-icon {{ in_array($order->status, ['processing', 'shipped', 'delivered']) ? 'active' : '' }}">
                        <i class="fas fa-{{ in_array($order->status, ['processing', 'shipped', 'delivered']) ? 'check' : 'dollar-sign' }} small"></i>
                    </div>
                    <div class="timeline-label">Pembayaran</div>
                </div>
                <div class="timeline-step">
                    <div class="timeline-icon {{ in_array($order->status, ['processing', 'shipped', 'delivered']) ? 'active' : '' }}">
                        <i class="fas fa-{{ in_array($order->status, ['processing', 'shipped', 'delivered']) ? 'check' : 'box' }} small"></i>
                    </div>
                    <div class="timeline-label">Diproses</div>
                </div>
                <div class="timeline-step">
                    <div class="timeline-icon {{ in_array($order->status, ['shipped', 'delivered']) ? 'active' : '' }}">
                        <i class="fas fa-{{ in_array($order->status, ['shipped', 'delivered']) ? 'check' : 'shipping-fast' }} small"></i>
                    </div>
                    <div class="timeline-label">Dikirim</div>
                </div>
                <div class="timeline-step">
                    <div class="timeline-icon {{ $order->status === 'delivered' ? 'active' : '' }}">
                        <i class="fas fa-{{ $order->status === 'delivered' ? 'check' : 'home' }} small"></i>
                    </div>
                    <div class="timeline-label">Diterima</div>
                </div>
            </div>
            
            @if($order->status === 'shipped')
                <div class="mt-3 alert alert-primary">
                    <div class="d-flex">
                        <i class="fas fa-info-circle fa-lg me-2 mt-1"></i>
                        <div>
                            <div class="fw-bold mb-1">Pesanan dalam pengiriman</div>
                            <p class="mb-0">Pesanan Anda sedang dalam perjalanan. Nomor resi: <strong>{{ $order->tracking_number ?? 'JP12345678ID' }}</strong></p>
                            <p class="mb-0">Estimasi tiba: <strong>{{ now()->addDays(3)->format('d M Y') }}</strong></p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Order Information -->
        <div class="order-section">
            <div class="section-title">
                <i class="fas fa-info-circle"></i> Informasi Pesanan
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="order-info-row">
                        <span class="order-info-label">Nomor Pesanan:</span>
                        <span>{{ $order->order_number }}</span>
                    </div>
                    <div class="order-info-row">
                        <span class="order-info-label">Tanggal Pemesanan:</span>
                        <span>{{ $order->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="order-info-row">
                        <span class="order-info-label">Status Pembayaran:</span>
                        <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">
                            {{ $order->payment_status === 'paid' ? 'Lunas' : 'Menunggu Pembayaran' }}
                        </span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="order-info-row">
                        <span class="order-info-label">Metode Pembayaran:</span>
                        <span>{{ $order->payment_method }}</span>
                    </div>
                    <div class="order-info-row">
                        <span class="order-info-label">Tanggal Pembayaran:</span>
                        <span>{{ $order->payment_status === 'paid' && $order->paid_at ? $order->paid_at->format('d M Y, H:i') : '-' }}</span>
                    </div>
                    @if($order->payment_status === 'pending')
                        <div class="order-info-row">
                            <span class="order-info-label">Batas Pembayaran:</span>
                            <span class="text-danger">{{ $order->payment_due_date->format('d M Y, H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Products Ordered -->
        <div class="order-section">
            <div class="section-title">
                <i class="fas fa-shopping-bag"></i> Produk yang Dibeli
            </div>
            
            @foreach($order->items as $item)
                <div class="product-item">
                    <img src="{{ asset($item->product->image) }}" class="product-image" alt="{{ $item->product->name }}">
                    <div class="product-details">
                        <div>
                            <div class="product-name">{{ $item->product->name }}</div>
                            <div class="text-muted small">{{ $item->quantity }} x Rp{{ number_format($item->price, 0, ',', '.') }}</div>
                        </div>
                        <div class="product-meta">
                            <a href="{{ route('products.show', $item->product->id) }}" class="text-decoration-none text-muted small">
                                <i class="fas fa-eye me-1"></i> Lihat Produk
                            </a>
                            <span class="fw-bold">Rp{{ number_format($item->quantity * $item->price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
            
            <!-- Order Summary -->
            <div class="mt-4 p-3 bg-light rounded">
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal</span>
                    <span>Rp{{ number_format($order->total_amount - $order->shipping_cost + $order->discount, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Pengiriman</span>
                    <span>Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                </div>
                @if($order->discount > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span>Diskon</span>
                        <span>-Rp{{ number_format($order->discount, 0, ',', '.') }}</span>
                    </div>
                @endif
                <hr>
                <div class="d-flex justify-content-between fw-bold">
                    <span>Total</span>
                    <span>Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        
        <!-- Shipping Information -->
        <div class="order-section">
            <div class="section-title">
                <i class="fas fa-truck"></i> Informasi Pengiriman
            </div>
            
            <div class="address-card">
                <div class="fw-bold">{{ $order->address->recipient_name }}</div>
                <div>{{ $order->address->phone }}</div>
                <div>{{ $order->address->full_address }}</div>
                <div>{{ $order->address->city }}, {{ $order->address->province }}, {{ $order->address->postal_code }}</div>
            </div>
            
            @if($order->status === 'shipped')
                <div class="mt-3">
                    <div class="fw-bold mb-2">Informasi Pengiriman</div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Kurir</span>
                        <span>JNE Regular</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Nomor Resi</span>
                        <span>{{ $order->tracking_number ?? 'JP12345678ID' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Estimasi Tiba</span>
                        <span>{{ now()->addDays(3)->format('d M Y') }}</span>
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Additional Information -->
        @if($order->notes)
            <div class="order-section">
                <div class="section-title">
                    <i class="fas fa-sticky-note"></i> Catatan Pesanan
                </div>
                
                <p class="mb-0">{{ $order->notes }}</p>
            </div>
        @endif
        
        
@if($order->status === 'cancelled')
<div class="order-section bg-danger-subtle">
    <div class="section-title text-danger">
        <i class="fas fa-exclamation-circle"></i> Informasi Pembatalan
    </div>
    
    <p>Pesanan ini telah dibatalkan pada {{ $order->updated_at ? $order->updated_at->format('d M Y, H:i') : '-' }}.</p>
    <p class="mb-0">Alasan: {{ session('cancellation_reason') ?? 'Dibatalkan oleh pembeli' }}.</p>
</div>
@endif
    </div>
</div>

<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelOrderModalLabel">Batalkan Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('orders.cancel', $order->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin membatalkan pesanan ini?</p>
                    <div class="mb-3">
                        <label for="cancellation_reason" class="form-label">Alasan Pembatalan</label>
                        <select class="form-select" id="cancellation_reason" name="cancellation_reason">
                            <option value="Berubah pikiran">Saya berubah pikiran</option>
                            <option value="Menemukan harga lebih murah">Menemukan harga yang lebih murah</option>
                            <option value="Salah input produk/alamat">Salah input produk/alamat</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                    <button type="submit" class="btn btn-danger">Ya, Batalkan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Confirm Receipt Modal -->
<div class="modal fade" id="confirmReceiptModal" tabindex="-1" aria-labelledby="confirmReceiptModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmReceiptModalLabel">Konfirmasi Penerimaan Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('orders.confirm', $order->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <p>Apakah Anda sudah menerima pesanan ini? Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Belum</button>
                    <button type="submit" class="btn btn-success">Ya, Saya Sudah Terima</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
