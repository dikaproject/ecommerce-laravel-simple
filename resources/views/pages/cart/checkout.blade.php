<!-- resources/views/pages/cart/checkout.blade.php -->
@extends('layouts.app')

@section('title', 'Checkout - Izzi Craft')

@section('styles')
<style>
    .checkout-header {
        background-color: #f8f9fa;
        padding: 40px 0 20px;
        margin-bottom: 30px;
    }
    
    .checkout-section {
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
    
    .address-card {
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .address-card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .address-card.selected {
        border-color: #dc3545;
        background-color: #fff8f8;
    }
    
    .payment-method {
        margin-bottom: 15px;
        padding: 15px;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        cursor: pointer;
    }
    
    .payment-method:hover {
        background-color: #f8f9fa;
    }
    
    .payment-method.selected {
        border-color: #dc3545;
        background-color: #fff8f8;
    }
    
    .payment-method img {
        height: 30px;
        object-fit: contain;
    }
    
    .cart-item {
        padding: 15px;
        margin-bottom: 15px;
        border: 1px solid #dee2e6;
        border-radius: 5px;
    }
    
    .product-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 5px;
    }
    
    .product-name {
        font-size: 0.9rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .item-price {
        font-weight: 500;
        color: #dc3545;
        font-size: 0.9rem;
    }
    
    .order-summary {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 5px;
        position: sticky;
        top: 20px;
    }
    
    .summary-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 15px;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    
    .summary-total {
        font-size: 1.3rem;
        font-weight: 700;
        color: #dc3545;
    }
</style>
@endsection

@section('content')
<!-- Checkout Header -->
<div class="checkout-header">
    <div class="container">
        <h1 class="fw-bold">Checkout</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cart.index') }}" class="text-decoration-none">Keranjang</a></li>
                <li class="breadcrumb-item active" aria-current="page">Checkout</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <div class="row">
        <!-- Left Column - Forms -->
        <div class="col-lg-8 mb-4">
            <form id="checkout-form" action="{{ route('orders.store') }}" method="POST">
                @csrf
                <!-- Shipping Address -->
                <div class="checkout-section">
                    <div class="section-title">
                        <i class="fas fa-map-marker-alt"></i> Alamat Pengiriman
                    </div>
                    
                    @if(count($addresses) > 0)
                        <div class="mb-3">
                            @foreach($addresses as $address)
                                <div class="address-card {{ $loop->first ? 'selected' : '' }}">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="address_id" 
                                               id="address-{{ $address->id }}" value="{{ $address->id }}"
                                               {{ $loop->first ? 'checked' : '' }}
                                               onchange="selectAddress(this)">
                                        <label class="form-check-label" for="address-{{ $address->id }}">
                                            <div class="d-flex justify-content-between">
                                                <strong>{{ $address->recipient_name }}</strong>
                                                <span>{{ $address->phone }}</span>
                                            </div>
                                            <div>{{ $address->full_address }}</div>
                                            <div>{{ $address->city }}, {{ $address->province }}, {{ $address->postal_code }}</div>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-outline-danger" 
                                data-bs-toggle="modal" data-bs-target="#newAddressModal">
                            <i class="fas fa-plus me-1"></i> Tambah Alamat Baru
                        </button>
                    @else
                        <div class="alert alert-info mb-3">
                            <p class="mb-0">Anda belum memiliki alamat pengiriman. Silakan tambahkan alamat baru.</p>
                        </div>
                        <button type="button" class="btn btn-danger" 
                                data-bs-toggle="modal" data-bs-target="#newAddressModal">
                            <i class="fas fa-plus me-1"></i> Tambah Alamat
                        </button>
                    @endif
                </div>
                
                <!-- Shipping Method -->
                <div class="checkout-section">
                    <div class="section-title">
                        <i class="fas fa-truck"></i> Metode Pengiriman
                    </div>
                    
                    <div class="mb-3">
                        @foreach($shippingMethods as $method)
                            <div class="payment-method {{ $loop->first ? 'selected' : '' }}">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="shipping_method" 
                                           id="shipping-{{ $method->id }}" value="{{ $method->id }}"
                                           {{ $loop->first ? 'checked' : '' }}
                                           data-price="{{ $method->price }}"
                                           onchange="selectShippingMethod(this)">
                                    <label class="form-check-label" for="shipping-{{ $method->id }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $method->name }}</strong>
                                                <p class="text-muted mb-0 small">Estimasi tiba {{ $method->estimated_delivery }}</p>
                                            </div>
                                            <div class="text-end">
                                                <span class="fw-bold">Rp{{ number_format($method->price, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Payment Method -->
                <div class="checkout-section">
                    <div class="section-title">
                        <i class="fas fa-credit-card"></i> Metode Pembayaran
                    </div>
                    
                    <div class="mb-3">
                        @foreach($paymentMethods as $payment)
                            <div class="payment-method {{ $loop->first ? 'selected' : '' }}">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" 
                                           id="payment-{{ $payment->id }}" value="{{ $payment->id }}"
                                           {{ $loop->first ? 'checked' : '' }}
                                           onchange="selectPaymentMethod(this)">
                                    <label class="form-check-label" for="payment-{{ $payment->id }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                @if($payment->logo)
                                                    <img src="{{ asset('images/Midtrans.webp') }}" alt="{{ $payment->name }}" class="me-2">
                                                @endif
                                                <div>
                                                    <strong>{{ $payment->name }}</strong>
                                                    @if($payment->description)
                                                        <p class="text-muted mb-0 small">{{ $payment->description }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Order Notes -->
                {{-- <div class="checkout-section">
                    <div class="section-title">
                        <i class="fas fa-sticky-note"></i> Catatan Pesanan (Opsional)
                    </div>
                    
                    <div class="mb-3">
                        <textarea class="form-control" id="order_notes" name="order_notes" rows="3" 
                                  placeholder="Tambahkan catatan untuk pesanan Anda..."></textarea>
                    </div>
                </div> --}}
            </form>
        </div>
        
        <!-- Right Column - Order Summary -->
        <div class="col-lg-4">
            <div class="order-summary">
                <h5 class="summary-title">Ringkasan Pesanan</h5>
                
                <!-- Item List -->
                <div class="mb-3">
                    @foreach($cartItems as $item)
                    <div class="cart-item">
                        <div class="d-flex">
                            <div class="me-2">
                                <img src="{{ asset($item->product->image) }}" class="product-image" alt="{{ $item->product->name }}">
                            </div>
                            <div class="flex-fill">
                                <div class="product-name">{{ $item->product->name }}</div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="item-price">Rp{{ number_format($item->product->price, 0, ',', '.') }}</div>
                                    <div class="text-muted small">x{{ $item->quantity }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Summary -->
                <div>
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="subtotal">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Diskon</span>
                        <span id="discount">-Rp{{ number_format($discount, 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Pengiriman</span>
                        <span id="shipping">Rp{{ number_format($shipping, 0, ',', '.') }}</span>
                    </div>
                    <hr>
                    <div class="summary-row summary-total">
                        <span>Total</span>
                        <span id="total">Rp{{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>
                
                <div class="d-grid gap-2 mt-3">
                    <button type="button" class="btn btn-danger btn-lg" onclick="document.getElementById('checkout-form').submit()">
                        Buat Pesanan
                    </button>
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary">
                        Kembali ke Keranjang
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Address Modal -->
<div class="modal fade" id="newAddressModal" tabindex="-1" aria-labelledby="newAddressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newAddressModalLabel">Tambah Alamat Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="new-address-form" action="{{ route('addresses.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="recipient_name" class="form-label">Nama Penerima</label>
                            <input type="text" class="form-control" id="recipient_name" name="recipient_name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Nomor Telepon</label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="col-12">
                            <label for="full_address" class="form-label">Alamat Lengkap</label>
                            <textarea class="form-control" id="full_address" name="full_address" rows="3" required></textarea>
                        </div>
                        <div class="col-md-4">
                            <label for="province" class="form-label">Provinsi</label>
                            <input type="text" class="form-control" id="province" name="province" required>
                        </div>
                        <div class="col-md-4">
                            <label for="city" class="form-label">Kota/Kabupaten</label>
                            <input type="text" class="form-control" id="city" name="city" required>
                        </div>
                        <div class="col-md-4">
                            <label for="postal_code" class="form-label">Kode Pos</label>
                            <input type="text" class="form-control" id="postal_code" name="postal_code" required>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="set_as_default" name="set_as_default">
                                <label class="form-check-label" for="set_as_default">
                                    Jadikan sebagai alamat utama
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="document.getElementById('new-address-form').submit()">
                    Simpan Alamat
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function selectAddress(radio) {
        // Remove selected class from all address cards
        document.querySelectorAll('.address-card').forEach(card => {
            card.classList.remove('selected');
        });
        
        // Add selected class to the parent of the selected radio button
        radio.closest('.address-card').classList.add('selected');
    }
    
    function selectShippingMethod(radio) {
        // Remove selected class from all shipping method cards
        document.querySelectorAll('.payment-method').forEach(method => {
            method.classList.remove('selected');
        });
        
        // Add selected class to the parent of the selected radio button
        radio.closest('.payment-method').classList.add('selected');
        
        // Update shipping cost in summary
        const shippingPrice = parseFloat(radio.dataset.price);
        document.getElementById('shipping').textContent = 'Rp' + shippingPrice.toLocaleString('id-ID');
        
        // Update total
        updateTotal();
    }
    
    function selectPaymentMethod(radio) {
        // Remove selected class from all payment method cards
        document.querySelectorAll('.payment-method').forEach(method => {
            method.classList.remove('selected');
        });
        
        // Add selected class to the parent of the selected radio button
        radio.closest('.payment-method').classList.add('selected');
    }
    
    function updateTotal() {
        const subtotal = parseFloat('{{ $subtotal }}');
        const discount = parseFloat('{{ $discount }}');
        
        // Get shipping cost from selected shipping method
        const selectedShippingMethod = document.querySelector('input[name="shipping_method"]:checked');
        const shipping = selectedShippingMethod ? parseFloat(selectedShippingMethod.dataset.price) : 0;
        
        // Calculate total
        const total = subtotal - discount + shipping;
        
        // Update total in summary
        document.getElementById('total').textContent = 'Rp' + total.toLocaleString('id-ID');
    }
</script>
@endsection