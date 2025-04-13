<!-- resources/views/pages/products/show.blade.php -->
@extends('layouts.app')

@section('title', $product->name . ' - Izzi Craft')

@section('styles')
<style>
    .detail-header {
        background-color: #dc3545;
        color: white;
        padding: 15px 0;
    }
    
    .back-button {
        color: white;
        font-size: 1.5rem;
        text-decoration: none;
        display: flex;
        align-items: center;
    }
    
    .back-button:hover {
        color: rgba(255, 255, 255, 0.8);
    }
    
    .product-image {
        width: 100%;
        height: auto;
        border-radius: 8px;
    }
    
    .product-title {
        font-size: 1.8rem;
        font-weight: bold;
        margin-top: 20px;
        margin-bottom: 10px;
    }
    
    .divider {
        height: 1px;
        background-color: #eee;
        margin: 20px 0;
    }
    
    .product-price {
        font-size: 1.8rem;
        font-weight: bold;
        color: #dc3545;
    }
    
    .price-tag {
        margin-bottom: 20px;
    }
    
    .product-description {
        color: #666;
        margin-bottom: 30px;
    }
    
    .quantity-selector {
        display: flex;
        align-items: center;
        margin-bottom: 30px;
    }
    
    .quantity-btn {
        width: 40px;
        height: 40px;
        border-radius: 5px;
        border: 1px solid #ddd;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        cursor: pointer;
    }
    
    .quantity-input {
        width: 60px;
        height: 40px;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin: 0 10px;
        text-align: center;
        font-size: 1.2rem;
    }
    
    .stock-info {
        font-size: 0.9rem;
        color: #666;
    }
    
    .action-buttons {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }
    
    .buy-now-btn {
        flex: 1;
        padding: 12px 0;
        background-color: #dc3545;
        color: white;
        border: none;
        border-radius: 5px;
        font-weight: bold;
        font-size: 1rem;
    }
    
    .add-to-cart-btn {
        flex: 1;
        padding: 12px 0;
        background-color: white;
        color: #dc3545;
        border: 1px solid #dc3545;
        border-radius: 5px;
        font-weight: bold;
        font-size: 1rem;
    }
</style>
@endsection

@section('content')
<!-- Product Detail Header -->
<div class="detail-header">
    <div class="container">
        <div class="d-flex align-items-center">
            <a href="javascript:history.back()" class="back-button me-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="h5 mb-0">Detail Produk</h1>
        </div>
    </div>
</div>

<div class="container py-4">
    <!-- Product Image -->
    <div class="text-center mb-3">
        <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="product-image">
    </div>
    
    <!-- Product Info -->
    <h1 class="product-title">{{ $product->name }}</h1>
    
    <div class="product-description">
        {{ $product->description }}
    </div>
    
    <div class="divider"></div>
    
    <!-- Price -->
    <div class="price-tag">
        <span class="product-price">Rp{{ number_format($product->price, 0, ',', '.') }}/item</span>
    </div>
    
    <!-- Add to Cart Form -->
    <form action="{{ route('cart.add') }}" method="POST" id="add-to-cart-form">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        
        <!-- Quantity Selector -->
        <div class="quantity-selector">
            <div class="quantity-btn" onclick="decrementQuantity()">
                <i class="fas fa-minus"></i>
            </div>
            <input type="number" id="quantity" name="quantity" class="quantity-input" 
                   value="1" min="1" max="{{ $product->stock }}" readonly>
            <div class="quantity-btn" onclick="incrementQuantity()">
                <i class="fas fa-plus"></i>
            </div>
            
            <span class="stock-info ms-3">Stok: {{ $product->stock }}</span>
        </div>
        
        <!-- Action Buttons -->
        <div class="action-buttons">
            <button type="submit" class="buy-now-btn" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                Beli Sekarang
            </button>
            <button type="button" class="add-to-cart-btn" onclick="addToCart()" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                Tambah Keranjang
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function decrementQuantity() {
        const quantityInput = document.getElementById('quantity');
        if (parseInt(quantityInput.value) > 1) {
            quantityInput.value = parseInt(quantityInput.value) - 1;
        }
    }
    
    function incrementQuantity() {
        const quantityInput = document.getElementById('quantity');
        const maxStock = {{ $product->stock }};
        if (parseInt(quantityInput.value) < maxStock) {
            quantityInput.value = parseInt(quantityInput.value) + 1;
        }
    }
    
    function addToCart() {
        const form = document.getElementById('add-to-cart-form');
        const formData = new FormData(form);
        
        fetch('{{ route('cart.add') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Produk berhasil ditambahkan ke keranjang');
            } else {
                alert(data.message || 'Gagal menambahkan produk ke keranjang');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menambahkan produk ke keranjang');
        });
    }
</script>
@endsection