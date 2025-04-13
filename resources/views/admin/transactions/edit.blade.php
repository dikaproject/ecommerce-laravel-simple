@extends('layouts.admin')

@section('title', 'Edit Pesanan')

@section('content-title', 'Edit Pesanan #' . $transaction->order_number)

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title">Detail Pesanan</h5>
                <a href="{{ route('admin.transactions.edit', $transaction->id) }}" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </div>
            <div class="admin-card-body">
                <form action="{{ route('admin.orders.update', $transaction->id) }}" method="POST" class="admin-form">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="order_number" class="form-label">Nomor Pesanan</label>
                                <input type="text" class="form-control" id="order_number" value="{{ $transaction->order_number }}" readonly>
                            </div>
                            
                            <div class="mb-3">
                                <label for="created_at" class="form-label">Tanggal Pesanan</label>
                                <input type="text" class="form-control" id="created_at" value="{{ $transaction->created_at->format('d M Y H:i') }}" readonly>
                            </div>
                            
                            <div class="mb-3">
                                <label for="customer_name" class="form-label">Nama Pelanggan</label>
                                <input type="text" class="form-control" id="customer_name" value="{{ $transaction->user->name }}" readonly>
                            </div>
                            
                            <div class="mb-3">
                                <label for="total_amount" class="form-label">Total Pesanan</label>
                                <input type="text" class="form-control" id="total_amount" value="Rp{{ number_format($transaction->total_amount, 0, ',', '.') }}" readonly>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Metode Pembayaran</label>
                                <input type="text" class="form-control" id="payment_method" value="{{ $transaction->payment_method }}" readonly>
                            </div>
                            
                            <div class="mb-3">
                                <label for="payment_status" class="form-label">Status Pembayaran</label>
                                <select class="form-select @error('payment_status') is-invalid @enderror" id="payment_status" name="payment_status">
                                    <option value="unpaid" {{ $transaction->payment_status == 'unpaid' ? 'selected' : '' }}>Belum Dibayar</option>
                                    <option value="paid" {{ $transaction->payment_status == 'paid' ? 'selected' : '' }}>Sudah Dibayar</option>
                                    <option value="refunded" {{ $transaction->payment_status == 'refunded' ? 'selected' : '' }}>Dikembalikan</option>
                                </select>
                                @error('payment_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="status" class="form-label">Status Pesanan</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                    <option value="pending" {{ $transaction->status == 'pending' ? 'selected' : '' }}>Menunggu</option>
                                    <option value="processing" {{ $transaction->status == 'processing' ? 'selected' : '' }}>Diproses</option>
                                    <option value="shipped" {{ $transaction->status == 'shipped' ? 'selected' : '' }}>Dikirim</option>
                                    <option value="delivered" {{ $transaction->status == 'delivered' ? 'selected' : '' }}>Diterima</option>
                                    <option value="cancelled" {{ $transaction->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-6">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title">Produk yang Dibeli</h5>
            </div>
            <div class="admin-card-body p-0">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Jumlah</th>
                            <th>Harga</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaction->items as $item)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($item->product && $item->product->image)
                                    <img src="{{ asset($item->product->image) }}" alt="{{ $item->product->name }}" width="40" class="me-2">
                                    @else
                                    <div class="bg-secondary me-2" style="width: 40px; height: 40px;"></div>
                                    @endif
                                    {{ $item->product ? $item->product->name : 'Produk tidak tersedia' }}
                                </div>
                            </td>
                            <td>{{ $item->quantity }}</td>
                            <td>Rp{{ number_format($item->price, 0, ',', '.') }}</td>
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
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Total:</td>
                            <td class="fw-bold">Rp{{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title">Informasi Pengiriman</h5>
            </div>
            <div class="admin-card-body">
                @if($transaction->address)
                <div class="mb-2">
                    <strong>Penerima:</strong> {{ $transaction->address->recipient_name }}
                </div>
                <div class="mb-2">
                    <strong>Telepon:</strong> {{ $transaction->address->phone }}
                </div>
                <div class="mb-2">
                    <strong>Alamat:</strong> {{ $transaction->address->full_address }}
                </div>
                <div class="mb-2">
                    <strong>Kota:</strong> {{ $transaction->address->city }}
                </div>
                <div class="mb-2">
                    <strong>Provinsi:</strong> {{ $transaction->address->province }}
                </div>
                <div class="mb-2">
                    <strong>Kode Pos:</strong> {{ $transaction->address->postal_code }}
                </div>
                @else
                <div class="alert alert-warning">Alamat pengiriman tidak tersedia</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection