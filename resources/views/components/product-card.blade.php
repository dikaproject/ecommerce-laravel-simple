<!-- resources/views/components/product-card.blade.php -->
<div class="card product-card h-100 border d-flex flex-column">
    <div class="img-container" style="height: 200px; overflow: hidden;">
        <a href="{{ route('products.show', $product->id) }}" class="text-decoration-none">
            <img src="{{ asset($product->image) }}" class="card-img-top w-100 h-100 object-fit-cover" alt="{{ $product->name }}">
        </a>
    </div>
    <a href="{{ route('products.show', $product->id) }}" class="text-decoration-none flex-grow-1">
        <div class="card-body">
            <h5 class="card-title text-dark">{{ $product->name }}</h5>
            <p class="card-text fw-bold text-danger">Rp{{ number_format($product->price, 0, ',', '.') }}</p>
            <div class="d-flex align-items-center mb-2">
                <div class="me-2">
                    <span class="text-warning">
                        @for ($i = 0; $i < 5; $i++)
                            <i class="fas fa-star{{ $i < $product->rating ? '' : '-o' }} small"></i>
                        @endfor
                    </span>
                </div>
                <small class="text-muted">{{ $product->sold ?? 0 }}+ terjual</small>
            </div>
        </div>
    </a>
    <div class="card-footer bg-white border-top-0 mt-auto">
        <div class="d-grid">
            <form action="{{ route('cart.add') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                    <i class="fas fa-cart-plus me-1"></i> Tambah ke Keranjang
                </button>
            </form>
        </div>
    </div>
</div>