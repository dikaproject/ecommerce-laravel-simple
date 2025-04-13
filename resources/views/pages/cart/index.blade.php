<!-- resources/views/pages/cart/index.blade.php -->
@extends('layouts.app')

@section('title', 'Keranjang Belanja - Izzi Craft')

@section('styles')
<style>
    .cart-header {
        background-color: #f8f9fa;
        padding: 40px 0 20px;
        margin-bottom: 30px;
    }
    
    .cart-item {
        padding: 15px;
        margin-bottom: 15px;
        border: 1px solid #dee2e6;
        border-radius: 5px;
    }
    
    .cart-item:hover {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .product-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 5px;
    }
    
    .product-name {
        max-width: 60ch;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .item-price {
        font-weight: 500;
        color: #dc3545;
    }
    
    .quantity-control {
        width: 120px;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .quantity-control .btn {
        background-color: #f8f9fa;
        border: none;
        color: #dc3545;
        font-weight: bold;
        padding: 8px 12px;
        transition: all 0.2s;
    }
    
    .quantity-control .btn:hover {
        background-color: #dc3545;
        color: white;
    }
    
    .quantity-control input {
        border: none;
        font-weight: bold;
        text-align: center;
        box-shadow: none;
        background-color: #fff;
    }
    
    .quantity-control input:focus {
        box-shadow: none;
        border: none;
    }
    
    .quantity-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background-color: #dc3545;
        color: white;
        border-radius: 50%;
        width: 22px;
        height: 22px;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .item-subtotal {
        font-weight: 600;
        font-size: 1.1rem;
    }
    
    .cart-summary {
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
    
    .empty-cart {
        text-align: center;
        padding: 50px 0;
    }
    
    .empty-cart-icon {
        font-size: 5rem;
        color: #dee2e6;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<!-- Cart Header -->
<div class="cart-header">
    <div class="container">
        <h1 class="fw-bold">Keranjang Belanja</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Keranjang</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    @if(count($cartItems) > 0)
    <!-- Temporary debug output -->
    <div class="alert alert-info mb-3" style="display: none;">
        <h5>Debug Information:</h5>
        <pre>{{ json_encode($cartItems->toArray(), JSON_PRETTY_PRINT) }}</pre>
    </div>
    
    <div class="row">
        <!-- Cart Items -->
        <div class="col-lg-8 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="selectAll" onchange="selectAllItems()">
                    <label class="form-check-label fw-bold" for="selectAll">Pilih Semua</label>
                </div>
                <button class="btn btn-outline-danger btn-sm" onclick="removeSelectedItems()">
                    <i class="fas fa-trash-alt me-1"></i> Hapus Terpilih
                </button>
            </div>
            
            <!-- Cart Items List -->
            @foreach($cartItems as $item)
            <div class="cart-item" id="cart-item-{{ $item->id }}">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="form-check">
                            <input class="form-check-input cart-item-checkbox" type="checkbox" 
                                   id="item-{{ $item->id }}" value="{{ $item->id }}">
                        </div>
                    </div>
                    <div class="col-auto position-relative">
                        <img src="{{ asset($item->product->image) }}" class="product-image" alt="{{ $item->product->name }}">
                        <span class="quantity-badge">{{ $item->quantity }}</span>
                    </div>
                    <div class="col">
                        <h5 class="product-name mb-1">
                            <a href="{{ route('products.show', $item->product->id) }}" class="text-decoration-none text-dark">
                                {{ $item->product->name }}
                            </a>
                        </h5>
                        <p class="item-price mb-0">Rp{{ number_format($item->product->price, 0, ',', '.') }}</p>
                    </div>
                    <div class="col-auto">
                        <div class="input-group quantity-control">
                            <button type="button" class="btn" 
                                    onclick="updateQuantity({{ $item->id }}, 'decrement')">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="form-control" value="{{ $item->quantity }}" 
                                   min="1" max="{{ $item->product->stock }}" 
                                   id="quantity-input-{{ $item->id }}"
                                   onchange="updateQuantity({{ $item->id }}, 'direct', this.value)">
                            <button type="button" class="btn" 
                                    onclick="updateQuantity({{ $item->id }}, 'increment')">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-auto text-end">
                        <p class="item-subtotal mb-1">
                            Rp{{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}
                        </p>
                        <button class="btn btn-link text-danger p-0" 
                                onclick="removeItem({{ $item->id }})">
                            <i class="fas fa-trash-alt me-1"></i> Hapus
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Cart Summary -->
        <div class="col-lg-4">
            <div class="cart-summary">
                <h5 class="summary-title">Ringkasan Belanja</h5>
                <div class="summary-row">
                    <span>Total Harga ({{ count($cartItems) }} produk)</span>
                    <span id="subtotal">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row">
                    <span>Diskon Produk</span>
                    <span id="discount">-Rp{{ number_format($discount, 0, ',', '.') }}</span>
                </div>
                <hr>
                <div class="summary-row summary-total">
                    <span>Total</span>
                    <span id="total">Rp{{ number_format($total, 0, ',', '.') }}</span>
                </div>
                <div class="d-grid gap-2 mt-3">
                    <a href="{{ route('cart.checkout') }}" class="btn btn-danger btn-lg">
                        Checkout
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                        Lanjut Belanja
                    </a>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- Empty Cart -->
    <div class="empty-cart">
        <div class="empty-cart-icon">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <h3>Keranjang Belanja Anda Kosong</h3>
        <p class="mb-4">Jelajahi produk dan tambahkan ke keranjang untuk mulai berbelanja</p>
        <a href="{{ route('products.index') }}" class="btn btn-danger btn-lg">
            Mulai Belanja
        </a>
    </div>
    @endif
</div>

<!-- Related Products -->
@if(count($cartItems) > 0)
<section class="py-5 bg-light">
    <div class="container">
        <h3 class="mb-4">Mungkin Anda Juga Suka</h3>
        <div class="row row-cols-2 row-cols-md-4 row-cols-lg-5 g-3">
            @foreach($recommendedProducts as $product)
                <div class="col">
                    @include('components.product-card', ['product' => $product])
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection

@section('scripts')
<script>
    function updateQuantity(itemId, action, value = null) {
        let quantityInput = document.querySelector(`#quantity-input-${itemId}`);
        let currentValue = parseInt(quantityInput.value);
        let newValue;
        
        if (action === 'increment') {
            newValue = currentValue + 1;
        } else if (action === 'decrement') {
            newValue = currentValue - 1;
            if (newValue < 1) newValue = 1;
        } else {
            newValue = parseInt(value);
            if (isNaN(newValue) || newValue < 1) newValue = 1;
        }
        
        // Show loading state
        const originalBtnContent = Array.from(document.querySelectorAll(`#cart-item-${itemId} .quantity-control .btn`))
            .map(btn => btn.innerHTML);
        
        document.querySelectorAll(`#cart-item-${itemId} .quantity-control .btn`).forEach(btn => {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        });
        
        // AJAX request to update quantity
        fetch('{{ route('cart.update') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                item_id: itemId,
                quantity: newValue
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update UI
                quantityInput.value = newValue;
                
                // Update the quantity badge
                const quantityBadge = document.querySelector(`#cart-item-${itemId} .quantity-badge`);
                if (quantityBadge) {
                    quantityBadge.textContent = newValue;
                    
                    // Add animation effect
                    quantityBadge.classList.add('animate__animated', 'animate__bounceIn');
                    setTimeout(() => {
                        quantityBadge.classList.remove('animate__animated', 'animate__bounceIn');
                    }, 1000);
                }
                
                // Update subtotal for this item
                const itemPrice = parseFloat(data.item_price);
                const itemSubtotal = itemPrice * newValue;
                document.querySelector(`#cart-item-${itemId} .item-subtotal`).textContent = 
                    'Rp' + itemSubtotal.toLocaleString('id-ID');
                
                // Update cart summary
                document.getElementById('subtotal').textContent = 'Rp' + data.subtotal.toLocaleString('id-ID');
                document.getElementById('discount').textContent = '-Rp' + data.discount.toLocaleString('id-ID');
                document.getElementById('total').textContent = 'Rp' + data.total.toLocaleString('id-ID');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal mengupdate jumlah produk.');
        })
        .finally(() => {
            // Restore button state
            document.querySelectorAll(`#cart-item-${itemId} .quantity-control .btn`).forEach((btn, i) => {
                btn.disabled = false;
                btn.innerHTML = originalBtnContent[i];
            });
        });
    }
    
    function removeItem(itemId) {
        if (confirm('Apakah Anda yakin ingin menghapus produk ini dari keranjang?')) {
            // AJAX request to remove item
            fetch('{{ route('cart.remove') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    item_id: itemId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove item from DOM
                    document.getElementById(`cart-item-${itemId}`).remove();
                    
                    // Update cart summary
                    document.getElementById('subtotal').textContent = 'Rp' + data.subtotal.toLocaleString('id-ID');
                    document.getElementById('discount').textContent = '-Rp' + data.discount.toLocaleString('id-ID');
                    document.getElementById('total').textContent = 'Rp' + data.total.toLocaleString('id-ID');
                    
                    // If cart is empty, reload the page
                    if (data.count === 0) {
                        location.reload();
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal menghapus produk dari keranjang.');
            });
        }
    }
    
    function selectAllItems() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const itemCheckboxes = document.querySelectorAll('.cart-item-checkbox');
        
        itemCheckboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
    }
    
    function removeSelectedItems() {
        const selectedCheckboxes = document.querySelectorAll('.cart-item-checkbox:checked');
        
        if (selectedCheckboxes.length === 0) {
            alert('Silakan pilih produk yang ingin dihapus.');
            return;
        }
        
        if (confirm(`Apakah Anda yakin ingin menghapus ${selectedCheckboxes.length} produk dari keranjang?`)) {
            const selectedIds = Array.from(selectedCheckboxes).map(checkbox => checkbox.value);
            
            // AJAX request to remove selected items
            fetch('{{ route('cart.remove-selected') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    item_ids: selectedIds
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove items from DOM
                    selectedIds.forEach(id => {
                        document.getElementById(`cart-item-${id}`).remove();
                    });
                    
                    // Update cart summary
                    document.getElementById('subtotal').textContent = 'Rp' + data.subtotal.toLocaleString('id-ID');
                    document.getElementById('discount').textContent = '-Rp' + data.discount.toLocaleString('id-ID');
                    document.getElementById('total').textContent = 'Rp' + data.total.toLocaleString('id-ID');
                    
                    // If cart is empty, reload the page
                    if (data.count === 0) {
                        location.reload();
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal menghapus produk dari keranjang.');
            });
        }
    }
</script>
@endsection