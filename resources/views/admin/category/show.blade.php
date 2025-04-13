@extends('layouts.admin')

@section('title', 'Detail Kategori')

@section('content-title', 'Detail Kategori')

@section('styles')
<style>
    .category-image {
        width: 100%;
        max-height: 250px;
        object-fit: contain;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    
    .category-info-label {
        font-weight: 500;
        color: #6c757d;
        min-width: 120px;
    }
    
    .category-info-value {
        font-weight: 600;
    }
    
    .product-item {
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 10px;
        margin-bottom: 15px;
        transition: all 0.2s;
    }
    
    .product-item:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .product-image {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 5px;
    }
    
    .product-title {
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 5px;
    }
    
    .product-price {
        color: #dc3545;
        font-weight: 600;
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Kategori</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
        </ol>
    </nav>
    <div>
        <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-primary">
            <i class="fas fa-edit me-1"></i> Edit
        </a>
        <a href="#" class="btn btn-danger" 
           onclick="event.preventDefault(); if(confirm('Apakah Anda yakin ingin menghapus kategori ini?')) document.getElementById('delete-category').submit();">
            <i class="fas fa-trash me-1"></i> Hapus
        </a>
        <form id="delete-category" action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-none">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>

<div class="row">
    <!-- Category Details -->
    <div class="col-md-4">
        <div class="admin-card">
            <div class="admin-card-body">
                <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" class="category-image">
                
                <h3 class="mb-4">{{ $category->name }}</h3>
                
                <div class="d-flex mb-3">
                    <span class="category-info-label">ID Kategori:</span>
                    <span class="category-info-value">#{{ $category->id }}</span>
                </div>
                
                <div class="d-flex mb-3">
                    <span class="category-info-label">Slug:</span>
                    <span class="category-info-value">{{ $category->slug }}</span>
                </div>
                
                
                <div class="d-flex mb-3">
                    <span class="category-info-label">Jumlah Produk:</span>
                    <span class="category-info-value">{{ $category->products_count }}</span>
                </div>
                
                <div class="d-flex mb-3">
                    <span class="category-info-label">Dibuat Pada:</span>
                    <span class="category-info-value">{{ $category->created_at->format('d M Y, H:i') }}</span>
                </div>
                
                <div class="mt-4">
                    <h5>Deskripsi</h5>
                    <p>{{ $category->description ?? 'Tidak ada deskripsi' }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Category Products -->
    <div class="col-md-8">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title">Produk dalam Kategori</h5>
                <a href="{{ route('admin.products.create') }}?category_id={{ $category->id }}" class="btn btn-sm btn-outline-danger">Tambah Produk</a>
            </div>
            <div class="admin-card-body">
                @if(count($products) > 0)
                    @foreach($products as $product)
                    <div class="product-item">
                        <div class="row align-items-center">
                            <div class="col-2">
                                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="product-image">
                            </div>
                            <div class="col-6">
                                <div class="product-title">{{ $product->name }}</div>
                                <div class="small text-muted">SKU: {{ $product->sku }}</div>
                            </div>
                            <div class="col-2">
                                <div class="product-price">Rp{{ number_format($product->price, 0, ',', '.') }}</div>
                                <div class="small text-muted">Stok: {{ $product->stock }}</div>
                            </div>
                            <div class="col-2 text-end">
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <p class="mb-0">Belum ada produk dalam kategori ini.</p>
                        <a href="{{ route('admin.products.create') }}?category_id={{ $category->id }}" class="btn btn-danger mt-3">
                            Tambah Produk Baru
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection