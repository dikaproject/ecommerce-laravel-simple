@extends('layouts.admin')

@section('title', 'Daftar Transaksi')

@section('content-title', 'Daftar Transaksi')

@section('styles')
<style>
    .status-badge {
        padding: 0.35em 0.65em;
        border-radius: 0.25rem;
        font-size: 0.75em;
        font-weight: 700;
        text-transform: uppercase;
    }
    
    .status-success {
        background-color: #d4edda;
        color: #155724;
    }
    
    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .status-failed {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    .filter-card {
        margin-bottom: 1.5rem;
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Transaksi</h1>
        <p class="text-muted">Kelola semua transaksi pembayaran</p>
    </div>
    <div>
        <a href="{{ route('admin.transactions.export') }}" class="btn btn-success">
            <i class="fas fa-file-excel me-1"></i> Export Excel
        </a>
    </div>
</div>

<div class="row">
    <!-- Filters -->
    <div class="col-md-12">
        <div class="admin-card filter-card">
            <div class="admin-card-body">
                <form action="{{ route('admin.transactions.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Cari Transaksi</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="ID Transaksi / No. Pesanan">
                    </div>
                    <div class="col-md-3">
                        <label for="payment_method" class="form-label">Metode Pembayaran</label>
                        <select class="form-select" id="payment_method" name="payment_method">
                            <option value="">Semua Metode</option>
                            <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="credit_card" {{ request('payment_method') == 'credit_card' ? 'selected' : '' }}>Kartu Kredit</option>
                            <option value="e_wallet" {{ request('payment_method') == 'e_wallet' ? 'selected' : '' }}>E-Wallet</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Semua Status</option>
                            <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Berhasil</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Gagal</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-1"></i> Filter
                        </button>
                        <a href="{{ route('admin.transactions.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Transactions Table -->
    <div class="col-md-12">
        <div class="admin-card">
            <div class="admin-card-body p-0">
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID Transaksi</th>
                                <th>No. Pesanan</th>
                                <th>Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Metode Pembayaran</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                            <tr>
                                <td>#{{ $transaction->id }}</td>
                                <td>#{{ $transaction->order_number }}</td>
                                <td>{{ $transaction->created_at->format('d M Y, H:i') }}</td>
                                <td>{{ $transaction->user->name }}</td>
                                <td>
                                    @if($transaction->payment_method == 'bank_transfer')
                                        <span><i class="fas fa-university me-1"></i> Bank Transfer</span>
                                    @elseif($transaction->payment_method == 'credit_card')
                                        <span><i class="far fa-credit-card me-1"></i> Kartu Kredit</span>
                                    @elseif($transaction->payment_method == 'e_wallet')
                                        <span><i class="fas fa-wallet me-1"></i> E-Wallet</span>
                                    @else
                                        {{ $transaction->payment_method }}
                                    @endif
                                </td>
                                <td>Rp{{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                                <td>
                                    @if($transaction->status == 'delivered')
                                        <span class="status-badge status-success">Diterima</span>
                                    @elseif($transaction->status == 'processing')
                                        <span class="status-badge status-processing" style="background-color: #d1ecf1; color: #0c5460;">Diproses</span>
                                    @elseif($transaction->status == 'shipped')
                                        <span class="status-badge status-shipped" style="background-color: #d4edda; color: #155724;">Dikirim</span>
                                    @elseif($transaction->status == 'pending')
                                        <span class="status-badge status-pending">Pending</span>
                                    @elseif($transaction->status == 'cancelled')
                                        <span class="status-badge status-failed">Dibatalkan</span>
                                    @else
                                        <span class="status-badge">{{ $transaction->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.transactions.show', $transaction->id) }}" class="table-action" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($transaction->status == 'pending')
                                        <a href="#" class="table-action text-success" title="Konfirmasi" 
                                           onclick="event.preventDefault(); if(confirm('Konfirmasi transaksi ini?')) document.getElementById('confirm-transaction-{{ $transaction->id }}').submit();">
                                            <i class="fas fa-check"></i>
                                        </a>
                                        <form id="confirm-transaction-{{ $transaction->id }}" action="{{ route('admin.transactions.confirm', $transaction->id) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('PUT')
                                        </form>
                                    @endif
                                    @if(Route::has('admin.transactions.print'))
                                    <a href="{{ route('admin.transactions.print', $transaction->id) }}" class="table-action" title="Cetak" target="_blank">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">Tidak ada data transaksi</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $transactions->appends(request()->except('page'))->links() }}
        </div>
    </div>
</div>

<!-- Transaction Summary -->
<div class="row mt-4">
    <div class="col-md-4">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title">Transaksi Hari Ini</h5>
            </div>
            <div class="admin-card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">{{ $todayTransactionsCount }}</h3>
                        <p class="text-muted mb-0">Total Transaksi</p>
                    </div>
                    <div>
                        <h3 class="mb-0 text-success">Rp{{ number_format($todayTransactionsAmount, 0, ',', '.') }}</h3>
                        <p class="text-muted mb-0">Total Pendapatan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title">Transaksi Minggu Ini</h5>
            </div>
            <div class="admin-card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">{{ $weekTransactionsCount }}</h3>
                        <p class="text-muted mb-0">Total Transaksi</p>
                    </div>
                    <div>
                        <h3 class="mb-0 text-success">Rp{{ number_format($weekTransactionsAmount, 0, ',', '.') }}</h3>
                        <p class="text-muted mb-0">Total Pendapatan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title">Transaksi Bulan Ini</h5>
            </div>
            <div class="admin-card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">{{ $monthTransactionsCount }}</h3>
                        <p class="text-muted mb-0">Total Transaksi</p>
                    </div>
                    <div>
                        <h3 class="mb-0 text-success">Rp{{ number_format($monthTransactionsAmount, 0, ',', '.') }}</h3>
                        <p class="text-muted mb-0">Total Pendapatan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection