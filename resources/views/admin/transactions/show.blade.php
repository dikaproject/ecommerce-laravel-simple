@extends('layouts.admin')

@section('title', 'Detail Pesanan')

@section('content-title', 'Detail Pesanan #' . $transaction->order_number)

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title">Informasi Pesanan</h5>
                <div>
                    <a href="{{ route('admin.orders.edit', $transaction->id) }}" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('admin.transactions.index') }}" class="btn btn-sm btn-outline-secondary ms-2">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="admin-card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table">
                            <tr>
                                <th style="width: 200px;">Nomor Pesanan</th>
                                <td>{{ $transaction->order_number }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Pesanan</th>
                                <td>{{ $transaction->created_at instanceof \Carbon\Carbon ? $transaction->created_at->format('d M Y H:i') : $transaction->created_at }}</td>
                            </tr>
                            <tr>
                                <th>Pelanggan</th>
                                <td>
                                    <a href="{{ route('admin.customers.show', $transaction->user->id) }}">
                                        {{ $transaction->user->name }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $transaction->user->email }}</td>
                            </tr>
                            <tr>
                                <th>Telepon</th>
                                <td>{{ $transaction->user->phone ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <table class="table">
                            <tr>
                                <th style="width: 200px;">Metode Pembayaran</th>
                                <td>{{ $transaction->payment_method }}</td>
                            </tr>
                            <tr>
                                <th>Status Pembayaran</th>
                                <td>
                                    @if($transaction->payment_status == 'paid')
                                        <span class="badge bg-success">Sudah Dibayar</span>
                                    @elseif($transaction->payment_status == 'refunded')
                                        <span class="badge bg-warning">Dikembalikan</span>
                                    @else
                                        <span class="badge bg-danger">Belum Dibayar</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Status Pesanan</th>
                                <td>
                                    @if($transaction->status == 'pending')
                                        <span class="badge status-badge status-pending">Menunggu</span>
                                    @elseif($transaction->status == 'processing')
                                        <span class="badge status-badge status-processing" style="background-color: #d1ecf1; color: #0c5460;">Diproses</span>
                                    @elseif($transaction->status == 'shipped')
                                        <span class="badge status-badge status-shipped" style="background-color: #d4edda; color: #155724;">Dikirim</span>
                                    @elseif($transaction->status == 'delivered')
                                        <span class="badge status-badge status-delivered" style="background-color: #c3e6cb; color: #155724;">Diterima</span>
                                    @elseif($transaction->status == 'cancelled')
                                        <span class="badge status-badge status-cancelled" style="background-color: #f8d7da; color: #721c24;">Dibatalkan</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Total</th>
                                <td>
                                    <strong>Rp{{ number_format($transaction->total_amount, 0, ',', '.') }}</strong>
                                </td>
                            </tr>
                            @if($transaction->payment_due_date)
                            <tr>
                                <th>Batas Waktu Pembayaran</th>
                                <td>{{ $transaction->payment_due_date instanceof \Carbon\Carbon ? $transaction->payment_due_date->format('d M Y H:i') : $transaction->payment_due_date }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
                
                <!-- Status Update Form -->
                @if($transaction->status != 'cancelled' && $transaction->status != 'delivered')
                <div class="mt-4">
                    <form action="{{ route('admin.orders.status.update', $transaction->id) }}" method="POST" class="d-flex align-items-center">
                        @csrf
                        @method('PUT')
                        <label for="status" class="me-2">Perbarui Status:</label>
                        <select name="status" id="status" class="form-select me-2" style="width: 200px;">
                            <option value="pending" {{ $transaction->status == 'pending' ? 'selected' : '' }}>Menunggu</option>
                            <option value="processing" {{ $transaction->status == 'processing' ? 'selected' : '' }}>Diproses</option>
                            <option value="shipped" {{ $transaction->status == 'shipped' ? 'selected' : '' }}>Dikirim</option>
                            <option value="delivered" {{ $transaction->status == 'delivered' ? 'selected' : '' }}>Diterima</option>
                            <option value="cancelled" {{ $transaction->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                        <button type="submit" class="btn btn-danger">Perbarui</button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title">Produk yang Dibeli</h5>
            </div>
            <div class="admin-card-body p-0">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaction->items as $item)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($item->product && $item->product->image)
                                    <img src="{{ asset($item->product->image) }}" alt="{{ $item->product->name }}" width="50" class="me-2">
                                    @else
                                    <div class="bg-secondary me-2" style="width: 50px; height: 50px;"></div>
                                    @endif
                                    <div>
                                        <div class="fw-semibold">
                                            {{ $item->product ? $item->product->name : 'Produk tidak tersedia' }}
                                        </div>
                                        @if($item->product)
                                        <div class="small text-secondary">
                                            {{ $item->product->category ? $item->product->category->name : 'Tanpa kategori' }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Subtotal:</td>
                            <td>Rp{{ number_format($transaction->total_amount - $transaction->shipping_cost, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Biaya Pengiriman:</td>
                            <td>Rp{{ number_format($transaction->shipping_cost, 0, ',', '.') }}</td>
                        </tr>
                        @if($transaction->discount > 0)
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Diskon:</td>
                            <td>-Rp{{ number_format($transaction->discount, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Total:</td>
                            <td class="fw-bold">Rp{{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title">Informasi Pengiriman</h5>
            </div>
            <div class="admin-card-body">
                @if($transaction->address)
                <div class="mb-3">
                    <div class="fw-semibold mb-1">Penerima:</div>
                    <div>{{ $transaction->address->recipient_name }}</div>
                </div>
                <div class="mb-3">
                    <div class="fw-semibold mb-1">Telepon:</div>
                    <div>{{ $transaction->address->phone }}</div>
                </div>
                <div class="mb-3">
                    <div class="fw-semibold mb-1">Alamat Lengkap:</div>
                    <div>{{ $transaction->address->full_address }}</div>
                </div>
                <div class="mb-3">
                    <div class="fw-semibold mb-1">Kota:</div>
                    <div>{{ $transaction->address->city }}</div>
                </div>
                <div class="mb-3">
                    <div class="fw-semibold mb-1">Provinsi:</div>
                    <div>{{ $transaction->address->province }}</div>
                </div>
                <div class="mb-3">
                    <div class="fw-semibold mb-1">Kode Pos:</div>
                    <div>{{ $transaction->address->postal_code }}</div>
                </div>
                @else
                <div class="alert alert-warning">Alamat pengiriman tidak tersedia</div>
                @endif
            </div>
        </div>
        
        @if($transaction->payment_status == 'paid')
        <div class="admin-card mt-4">
            <div class="admin-card-header">
                <h5 class="admin-card-title">Informasi Pembayaran</h5>
            </div>
            <div class="admin-card-body">
                <div class="mb-3">
                    <div class="fw-semibold mb-1">ID Transaksi:</div>
                    <div>{{ $transaction->midtrans_id ?? 'Tidak tersedia' }}</div>
                </div>
                <div class="mb-3">
                    <div class="fw-semibold mb-1">Metode Pembayaran:</div>
                    <div>{{ $transaction->payment_method }}</div>
                </div>
                <div class="mb-3">
                    <div class="fw-semibold mb-1">Status:</div>
                    <div><span class="badge bg-success">Lunas</span></div>
                </div>
                <div class="mb-3">
                    <div class="fw-semibold mb-1">Tanggal Pembayaran:</div>
                    <div>{{ $transaction->updated_at instanceof \Carbon\Carbon ? $transaction->updated_at->format('d M Y H:i') : $transaction->updated_at }}</div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection