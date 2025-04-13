@extends('layouts.admin')

@section('title', 'Detail Pengguna')

@section('content-title', 'Detail Pengguna')

@section('styles')
<style>
    .card {
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 24px;
        border: none;
    }
    
    .card-header {
        background-color: #fff;
        border-bottom: 1px solid #f0f0f0;
        padding: 16px 20px;
        font-weight: 600;
    }
    
    .card-body {
        padding: 20px;
    }
    
    .avatar-lg {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .avatar-placeholder {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        color: #adb5bd;
    }
    
    .profile-info {
        flex: 1;
    }
    
    .info-label {
        font-weight: 500;
        color: #6c757d;
        width: 120px;
        display: inline-block;
    }
    
    .info-value {
        font-weight: 400;
        color: #212529;
    }
    
    .info-row {
        margin-bottom: 12px;
    }
    
    .user-role {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
    }
    
    .role-admin {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }
    
    .role-user {
        background-color: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
    }
    
    .table {
        font-size: 14px;
    }
    
    .table th {
        font-weight: 600;
        color: #333;
        background-color: #f8f9fa;
    }
    
    .status-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .empty-state {
        text-align: center;
        padding: 30px;
        color: #6c757d;
    }
    
    .empty-icon {
        font-size: 48px;
        margin-bottom: 15px;
        color: #dee2e6;
    }
</style>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0">Detail Pengguna</h1>
    <div>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
        <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-primary ms-2">
            <i class="fas fa-edit me-1"></i> Edit
        </a>
    </div>
</div>

<div class="row">
    <!-- Profile Information -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                Informasi Profil
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    @if($customer->avatar)
                        <img src="{{ asset($customer->avatar) }}" alt="{{ $customer->name }}" class="avatar-lg mb-3">
                    @else
                        <div class="avatar-placeholder mb-3 mx-auto">
                            <i class="fas fa-user"></i>
                        </div>
                    @endif
                    <h5 class="mb-0">{{ $customer->name }}</h5>
                    <p class="text-muted mb-2">{{ $customer->email }}</p>
                    <span class="user-role {{ $customer->role == 'admin' ? 'role-admin' : 'role-user' }}">
                        {{ ucfirst($customer->role) }}
                    </span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">ID</span>
                    <span class="info-value">#{{ $customer->id }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">No. Telepon</span>
                    <span class="info-value">{{ $customer->phone ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Terdaftar Pada</span>
                    <span class="info-value">{{ $customer->created_at->format('d M Y H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Terakhir Update</span>
                    <span class="info-value">{{ $customer->updated_at->format('d M Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Order History -->
    <div class="col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Riwayat Transaksi</span>
                <span class="badge bg-primary">{{ $customer->transactions->count() }} Transaksi</span>
            </div>
            <div class="card-body p-0">
                @if($customer->transactions->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customer->transactions as $transaction)
                            <tr>
                                <td>#{{ $transaction->id }}</td>
                                <td>{{ $transaction->created_at->format('d M Y') }}</td>
                                <td>Rp{{ number_format($transaction->total, 0, ',', '.') }}</td>
                                <td>
                                    @php
                                        $statusClass = '';
                                        switch($transaction->status) {
                                            case 'pending':
                                                $statusClass = 'bg-warning';
                                                break;
                                            case 'processing':
                                                $statusClass = 'bg-info';
                                                break;
                                            case 'completed':
                                                $statusClass = 'bg-success';
                                                break;
                                            case 'cancelled':
                                                $statusClass = 'bg-danger';
                                                break;
                                            default:
                                                $statusClass = 'bg-secondary';
                                        }
                                    @endphp
                                    <span class="status-badge {{ $statusClass }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $transaction->id) }}" class="btn btn-sm btn-outline-primary">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <p>Pengguna ini belum memiliki transaksi</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Addresses -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Alamat Tersimpan</span>
                <span class="badge bg-primary">{{ $customer->addresses->count() }} Alamat</span>
            </div>
            @if($customer->addresses->count() > 0)
            <div class="card-body">
                <div class="row">
                    @foreach($customer->addresses as $address)
                    <div class="col-md-6 mb-3">
                        <div class="border rounded p-3">
                            <h6 class="mb-2">{{ $address->label ?? 'Alamat ' . $loop->iteration }}</h6>
                            <p class="mb-1"><strong>{{ $address->recipient_name }}</strong></p>
                            <p class="mb-1">{{ $address->recipient_phone }}</p>
                            <p class="mb-1">{{ $address->address_line }}, {{ $address->city }}</p>
                            <p class="mb-1">{{ $address->province }}, {{ $address->postal_code }}</p>
                            @if($address->is_default)
                            <span class="badge bg-success mt-2">Alamat Utama</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="card-body">
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <p>Pengguna ini belum menyimpan alamat</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
