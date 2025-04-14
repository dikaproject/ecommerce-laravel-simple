@extends('layouts.app')

@section('title', 'Riwayat Pesanan - Izzi Craft')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Riwayat Pesanan</h2>
                <a href="{{ route('profile') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Profil
                </a>
            </div>
            <p class="text-muted">Semua pesanan yang pernah Anda lakukan</p>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if(count($transactions) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID Pesanan</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th>Metode Pembayaran</th>
                                        <th>Total</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                    <tr>
                                        <td>#{{ $transaction->id }}</td>
                                        <td>{{ $transaction->created_at->format('d M Y, H:i') }}</td>
                                        <td>
                                            @php
                                                $statusClass = 'secondary';
                                                switch($transaction->status) {
                                                    case 'pending':
                                                        $statusClass = 'warning';
                                                        $statusText = 'Menunggu Pembayaran';
                                                        break;
                                                    case 'processing':
                                                        $statusClass = 'info';
                                                        $statusText = 'Diproses';
                                                        break;
                                                    case 'shipped':
                                                        $statusClass = 'primary';
                                                        $statusText = 'Dikirim';
                                                        break;
                                                    case 'completed':
                                                        $statusClass = 'success';
                                                        $statusText = 'Selesai';
                                                        break;
                                                    case 'cancelled':
                                                        $statusClass = 'danger';
                                                        $statusText = 'Dibatalkan';
                                                        break;
                                                    default:
                                                        $statusText = ucfirst($transaction->status);
                                                }
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
                                        </td>
                                        <td>{{ $transaction->payment_method ?? 'Belum dibayar' }}</td>
                                        <td>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                                        <td>
                                            <a href="{{ route('orders.show', $transaction->id) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                                            
                                            @if($transaction->status === 'pending')
                                                <a href="{{ route('payment.show', $transaction->id) }}" class="btn btn-sm btn-danger">Bayar</a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $transactions->links() }}
                        </div>
                        @else
                        <div class="text-center py-5">
                            <div class="empty-state-icon mb-4">
                                <i class="fas fa-shopping-bag fa-5x text-muted"></i>
                            </div>
                            <h5>Belum Ada Pesanan</h5>
                            <p class="text-muted">Anda belum melakukan pemesanan apapun</p>
                            <a href="{{ route('products.index') }}" class="btn btn-danger">Mulai Belanja</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Order Status Information -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Informasi Status Pesanan</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-warning p-2 me-2">
                                    <i class="fas fa-clock fa-fw"></i>
                                </span>
                                <div>
                                    <strong>Menunggu Pembayaran</strong>
                                    <p class="text-muted small mb-0">Pesanan belum dibayar</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-info p-2 me-2">
                                    <i class="fas fa-cog fa-fw"></i>
                                </span>
                                <div>
                                    <strong>Diproses</strong>
                                    <p class="text-muted small mb-0">Pesanan sedang diproses</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary p-2 me-2">
                                    <i class="fas fa-shipping-fast fa-fw"></i>
                                </span>
                                <div>
                                    <strong>Dikirim</strong>
                                    <p class="text-muted small mb-0">Pesanan dalam pengiriman</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-success p-2 me-2">
                                    <i class="fas fa-check fa-fw"></i>
                                </span>
                                <div>
                                    <strong>Selesai</strong>
                                    <p class="text-muted small mb-0">Pesanan telah diterima</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-danger p-2 me-2">
                                    <i class="fas fa-times fa-fw"></i>
                                </span>
                                <div>
                                    <strong>Dibatalkan</strong>
                                    <p class="text-muted small mb-0">Pesanan telah dibatalkan</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .empty-state-icon {
        height: 120px;
        width: 120px;
        line-height: 120px;
        border-radius: 50%;
        background-color: #f8f9fa;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
        }
    }
</style>
@endsection