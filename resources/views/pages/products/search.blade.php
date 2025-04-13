<!-- resources/views/pages/products/search.blade.php -->
@extends('layouts.app')

@section('title', 'Hasil Pencarian untuk: ' . $query . ' - Izzi Craft')

@section('styles')
<style>
    .search-header {
        background-color: #f8f9fa;
        padding: 40px 0;
        margin-bottom: 30px;
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
</style>
@endsection

@section('content')
<!-- Search Header -->
<div class="search-header">
    <div class="container">
        <h1 class="h3 mb-3">Hasil Pencarian: "{{ $query }}"</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Hasil Pencarian</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container pb-5">
    <div class="row">
        <!-- Sidebar Filters (same as products.index) -->
        <div class="col-lg-3 mb-4">
            <div class="filter-sidebar">
                <h5 class="mb-3">Filter</h5>
                
                <form action="{{ route('products.search') }}" method="GET">
                    <input type="hidden" name="search" value="{{ $query }}">
                    
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
                    
                    <button type="submit" class="btn btn-danger w-100">Terapkan Filter</button>
                </form>
            </div>
        </div>
        
        <!-- Search Results -->
        <div class="col-lg-9">
            <!-- Sorting Options -->
            <div class="sorting-bar d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <span class="me-2">{{ $products->total() }} hasil ditemukan</span>
                </div>
                
                <div class="d-flex align-items-center">
                    <label for="sort-by" class="me-2">Urutkan:</label>
                    <select id="sort-by" class="form-select" onchange="window.location.href=this.value">
                        <option value="{{ route('products.search', ['search' => $query, 'sort' => 'relevance'] + request()->except('sort', 'page')) }}" 
                                {{ request('sort') == 'relevance' ? 'selected' : '' }}>
                            Paling Relevan
                        </option>
                        <option value="{{ route('products.search', ['search' => $query, 'sort' => 'price-low'] + request()->except('sort', 'page')) }}" 
                                {{ request('sort') == 'price-low' ? 'selected' : '' }}>
                            Harga: Rendah ke Tinggi
                        </option>
                        <option value="{{ route('products.search', ['search' => $query, 'sort' => 'price-high'] + request()->except('sort', 'page')) }}" 
                                {{ request('sort') == 'price-high' ? 'selected' : '' }}>
                            Harga: Tinggi ke Rendah
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
                    {{ $products->appends(request()->except('page'))->links() }}
                </div>
            @else
                <div class="alert alert-info text-center p-5">
                    <i class="fas fa-search fa-3x mb-3"></i>
                    <h4>Produk tidak ditemukan</h4>
                    <p>Maaf, kami tidak dapat menemukan produk yang sesuai dengan kata kunci Anda.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-danger mt-2">Lihat Semua Produk</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
