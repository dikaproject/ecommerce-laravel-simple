<!-- resources/views/pages/products/index.blade.php -->
@extends('layouts.app')

@section('title', 'Semua Produk - Izzi Craft')

@section('styles')
<style>
    .filter-sidebar {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 5px;
    }
    
    .price-range {
        margin-bottom: 20px;
    }
    
    .range-slider {
        width: 100%;
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
</style>
@endsection

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Beranda</a></li>
            <li class="breadcrumb-item active" aria-current="page">Semua Produk</li>
        </ol>
    </nav>
    
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-4">
            <div class="filter-sidebar">
                <h5 class="mb-3">Filter</h5>
                
                <form action="{{ route('products.index') }}" method="GET">
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
                    
                    <!-- Category Filter -->
                    <div class="filter-group">
                        <div class="filter-group-title">Kategori</div>
                        @foreach($categories as $category)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="categories[]" 
                                   value="{{ $category->id }}" id="category-{{ $category->id }}"
                                   {{ in_array($category->id, request('categories', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="category-{{ $category->id }}">
                                {{ $category->name }}
                            </label>
                        </div>
                        @endforeach
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
                    <span class="me-2">Showing {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products</span>
                </div>
                
                <div class="d-flex align-items-center">
                    <label for="sort-by" class="me-2">Urutkan:</label>
                    <select id="sort-by" class="form-select" onchange="window.location.href=this.value">
                        <option value="{{ route('products.index', ['sort' => 'price-high'] + request()->except('sort', 'page')) }}" 
                                {{ request('sort') == 'price-high' ? 'selected' : '' }}>
                            Harga: Tinggi ke Rendah
                        </option>
                        <option value="{{ route('products.index', ['sort' => 'rating'] + request()->except('sort', 'page')) }}" 
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
                            @include('components.product-card', ['product' => $product])
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="Product pagination">
                        <ul class="pagination">
                            {{-- Previous Page Link --}}
                            @if ($products->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">&laquo;</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $products->appends(request()->except('page'))->previousPageUrl() }}" rel="prev">&laquo;</a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                                @if ($page == $products->currentPage())
                                    <li class="page-item active" aria-current="page">
                                        <span class="page-link">{{ $page }}</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach

                            {{-- Next Page Link --}}
                            @if ($products->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $products->appends(request()->except('page'))->nextPageUrl() }}" rel="next">&raquo;</a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link">&raquo;</span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                </div>
            @else
                <div class="alert alert-info text-center p-5">
                    <i class="fas fa-search fa-3x mb-3"></i>
                    <h4>Produk tidak ditemukan</h4>
                    <p>Maaf, kami tidak dapat menemukan produk yang sesuai dengan filter Anda.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-danger mt-2">Lihat Semua Produk</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection