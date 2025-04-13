<!-- resources/views/pages/category/show.blade.php -->
@extends('layouts.app')

@section('title', $category->name . ' - Izzi Craft')

@section('styles')
<style>
    .category-header {
        background-color: #f8f9fa;
        padding: 40px 0;
        margin-bottom: 30px;
    }
    
    .category-image {
        max-height: 200px;
        object-fit: contain;
    }
    
    .filter-sidebar {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 5px;
    }
    
    .price-range {
        margin-bottom: 20px;
    }
    
    .filter-group {
        margin-bottom: 20px;
    }
    
    .filter-group-title {
        font-weight: 600;
        margin-bottom: 10px;
    }
    
    .sorting-bar {
        background-color: #f8f9fa;
        padding: 10px 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    
    .product-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection

@section('content')
<!-- Category Header -->
<div class="category-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('category.index') }}" class="text-decoration-none">Kategori</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
                    </ol>
                </nav>
                <h1 class="display-5 fw-bold">{{ $category->name }}</h1>
                <p class="lead">{{ $category->description }}</p>
            </div>
            <div class="col-md-4 text-center">
                <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" class="img-fluid category-image">
            </div>
        </div>
    </div>
</div>

<div class="container pb-5">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-4">
            <div class="filter-sidebar">
                <h5 class="mb-3">Filter</h5>
                
                <form action="{{ route('category.show', $category->slug) }}" method="GET">
                    <!-- Price Range Filter -->
                    <div class="filter-group price-range">
                        <div class="filter-group-title">Rentang Harga</div>
                        <div class="mb-3">
                            <label for="min-price" class="form-label">Minimal (Rp)</label>
                            <input type="number" class="form-control" id="min-price" name="min_price" 
                                   value="{{ request('min_price', '') }}" placeholder="Min">
                        </div>
                        <div class="mb-3">
                            <label for="max-price" class="form-label">Maksimal (Rp)</label>
                            <input type="number" class="form-control" id="max-price" name="max_price" 
                                   value="{{ request('max_price', '') }}" placeholder="Max">
                        </div>
                    </div>
                    
                    <hr>
                    
                    <!-- Rating Filter -->
                    <div class="filter-group">
                        <div class="filter-group-title">Rating</div>
                        @for($i = 5; $i >= 1; $i--)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="ratings[]" 
                                   value="{{ $i }}" id="rating-{{ $i }}"
                                   {{ in_array($i, request('ratings', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="rating-{{ $i }}">
                                @for($j = 0; $j < 5; $j++)
                                <i class="fas fa-star{{ $j < $i ? ' text-warning' : ' text-muted' }} small"></i>
                                @endfor
                                {{ $i == 5 ? 'ke atas' : '' }}
                            </label>
                        </div>
                        @endfor
                    </div>
                    
                    <button type="submit" class="btn btn-danger w-100">Terapkan Filter</button>
                </form>
            </div>
        </div>
        
        <!-- Product Listings -->
        <div class="col-lg-9">
            <!-- Sorting Options -->
            <div class="sorting-bar d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <span class="me-2">Menampilkan {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} dari {{ $products->total() }} produk</span>
                </div>
                
                <div class="d-flex align-items-center">
                    <label for="sort-by" class="me-2">Urutkan:</label>
                    <select id="sort-by" class="form-select" onchange="window.location.href=this.value">
                        <option value="{{ route('category.show', ['slug' => $category->slug, 'sort' => 'latest'] + request()->except('sort', 'page')) }}" 
                                {{ request('sort') == 'latest' ? 'selected' : '' }}>
                            Terbaru
                        </option>
                        <option value="{{ route('category.show', ['slug' => $category->slug, 'sort' => 'price-low'] + request()->except('sort', 'page')) }}" 
                                {{ request('sort') == 'price-low' ? 'selected' : '' }}>
                            Harga: Rendah ke Tinggi
                        </option>
                        <option value="{{ route('category.show', ['slug' => $category->slug, 'sort' => 'price-high'] + request()->except('sort', 'page')) }}" 
                                {{ request('sort') == 'price-high' ? 'selected' : '' }}>
                            Harga: Tinggi ke Rendah
                        </option>
                        <option value="{{ route('category.show', ['slug' => $category->slug, 'sort' => 'popular'] + request()->except('sort', 'page')) }}" 
                                {{ request('sort') == 'popular' ? 'selected' : '' }}>
                            Popularitas
                        </option>
                        <option value="{{ route('category.show', ['slug' => $category->slug, 'sort' => 'rating'] + request()->except('sort', 'page')) }}" 
                                {{ request('sort') == 'rating' ? 'selected' : '' }}>
                            Rating Tertinggi
                        </option>
                    </select>
                </div>
            </div>
            
            <!-- Products Grid -->
            @if($products->count() > 0)
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
                    @foreach($products as $product)
                        <div class="col">
                            <div class="card product-card h-100 border">
                                <a href="{{ route('products.show', $product->id) }}" class="text-decoration-none">
                                    <img src="{{ asset($product->image) }}" class="card-img-top" alt="{{ $product->name }}">
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
                                            <small class="text-muted">{{ $product->sold }}+ terjual</small>
                                        </div>
                                    </div>
                                </a>
                                <div class="card-footer bg-white border-top-0">
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
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->appends(request()->except('page'))->links() }}
                </div>
            @else
                <div class="alert alert-info text-center p-5">
                    <i class="fas fa-search fa-3x mb-3"></i>
                    <h4>Produk tidak ditemukan</h4>
                    <p>Maaf, kami tidak dapat menemukan produk yang sesuai dengan filter Anda.</p>
                    <a href="{{ route('category.show', $category->slug) }}" class="btn btn-outline-danger mt-2">Reset Filter</a>
                </div>
            @endif
            
            <!-- Category Description -->
            @if($category->long_description)
            <div class="mt-5">
                <h4>Tentang {{ $category->name }}</h4>
                <hr>
                <div class="category-description">
                    {!! $category->long_description !!}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Other Categories -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="mb-4">Kategori Lainnya</h2>
        <div class="row g-4">
            @foreach($otherCategories as $otherCategory)
                <div class="col-6 col-md-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <a href="{{ route('category.show', $otherCategory->slug) }}" class="text-decoration-none">
                            <img src="{{ asset($otherCategory->image) }}" class="card-img-top" alt="{{ $otherCategory->name }}" style="height: 150px; object-fit: cover;">
                            <div class="card-body text-center">
                                <h5 class="card-title text-dark">{{ $otherCategory->name }}</h5>
                                <p class="card-text text-muted small">{{ $otherCategory->products_count }} produk</p>
                            </div>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection