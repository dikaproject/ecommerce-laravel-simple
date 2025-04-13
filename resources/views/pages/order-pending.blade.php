<!-- resources/views/pages/order-pending.blade.php -->
@extends('layouts.app')

@section('title', 'Menunggu Pembayaran - Izzi Craft')

@section('styles')
<style>
    .pending-header {
        background-color: #f8f9fa;
        padding: 40px 0 20px;
        margin-bottom: 30px;
    }
    
    .pending-container {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .pending-section {
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        padding: 30px;
        margin-bottom: 20px;
    }
    
    .pending-icon {
        width: 80px;
        height: 80px;
        background-color: #fff3cd;
        color: #ffc107;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }
    
    .pending-icon i {
        font-size: 2.5rem;
    }
    
    .pending-title {
        font-size: 1.8rem;
        font-weight: 700;
        text-align: center;
        margin-bottom: 20px;
    }
    
    .pending-message {
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
</style>
@endsection

@section('content')
<!-- Pending Header -->
<div class="pending-header">
    <div class="container">
        <h1 class="fw-bold">Menunggu Pembayaran</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Menunggu Pembayaran</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <div class="pending-container">
        <!-- Pending Message -->
        <div class="pending-section">
            <div class="pending-icon">
                <i class="fas fa-clock"></i>
            </div>
            <h2 class="pending-title">Menunggu Pembayaran</h2>
            <div class="pending-message">
                <p>Pesanan Anda telah dibuat dan sedang menunggu pembayaran.</p>
                <p>Mohon segera selesaikan pembayaran Anda sebelum batas waktu yang ditentukan.</p>
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
                    <span class="order-info-label">Batas Waktu Pembayaran:</span>
                    <span>{{ $order->payment_due_date->format('d M Y, H:i') }}</span>
                </div>
                <div class="order-info-row">
                    <span class="order-info-label">Status Pesanan:</span>
                    <span class="badge bg-warning">Menunggu Pembayaran</span>
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
            
            <!-- Action Buttons -->
            <div class="d-grid gap-2 mt-4">
                <a href="{{ route('payment.show', $order->id) }}" class="btn btn-danger">
                    <i class="fas fa-credit-card me-1"></i> Lanjutkan Pembayaran
                </a>
                <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-clipboard-list me-1"></i> Lihat Semua Pesanan
                </a>
                <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-home me-1"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
