<!-- resources/views/pages/admin/products/show.blade.php -->
@extends('layouts.admin')

@section('title', 'Detail Produk')

@section('content-title', 'Detail Produk')

@section('styles')
<style>
    .product-main-image {
        width: 100%;
        max-height: 300px;
        object-fit: contain;
        border-radius: 5px;
        margin-bottom: 10px;
    }
    
    .product-thumbnail {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 5px;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.2s;
    }
    
    .product-thumbnail:hover {
        border-color: #dc3545;
    }
    
    .product-thumbnail.active {
        border-color: #dc3545;
    }
    
    .product-info-label {
        font-weight: 500;
        color: #6c757d;
        min-width: 120px;
    }
    
    .product-info-value {
        font-weight: 600;
    }
    
    .product-actions {
        position: absolute;
        top: 1rem;
        right: 1rem;
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Produk</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>
    <div>
        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary">
            <i class="fas fa-edit me-1"></i> Edit
        </a>
        <a href="#" class="btn btn-danger" 
           onclick="event.preventDefault(); if(confirm('Apakah Anda yakin ingin menghapus produk ini?')) document.getElementById('delete-product').submit();">
            <i class="fas fa-trash me-1"></i> Hapus
        </a>
        <form id="delete-product" action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-none">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>

<div class="row">
    <!-- Product Images -->
    <div class="col-md-5">
        <div class="admin-card">
            <div class="admin-card-body">
                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="product-main-image" id="main-product-image">
                
                @if($product->images && count($product->images) > 0)
                    <div class="d-flex gap-2 flex-wrap mt-2">
                        <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" 
                             class="product-thumbnail active" onclick="changeImage(this.src)">
                        
                        @foreach($product->images as $image)
                            <img src="{{ asset($image) }}" alt="{{ $product->name }}" 
                                 class="product-thumbnail" onclick="changeImage(this.src)">
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Product Information -->
    <div class="col-md-7">
        <div class="admin-card position-relative">
            <div class="admin-card-body">
                <h2 class="mb-4">{{ $product->name }}</h2>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="d-flex mb-3">
                            <span class="product-info-label">ID Produk:</span>
                            <span class="product-info-value">#{{ $product->id }}</span>
                        </div>
                        <div class="d-flex mb-3">
                            <span class="product-info-label">Kategori:</span>
                            <span class="product-info-value">{{ $product->category->name }}</span>
                        </div>
                        <div class="d-flex mb-3">
                            <span class="product-info-label">Harga:</span>
                            <span class="product-info-value text-danger">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex mb-3">
                            <span class="product-info-label">Stok:</span>
                            <span class="product-info-value">{{ $product->stock }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex mb-3">
                            <span class="product-info-label">SKU:</span>
                            <span class="product-info-value">{{ $product->sku }}</span>
                        </div>
                        <div class="d-flex mb-3">
                            <span class="product-info-label">Rating:</span>
                            <span class="product-info-value">
                                {{ number_format($product->rating, 1) }}
                                @for($i = 0; $i < 5; $i++)
                                    <i class="fas fa-star{{ $i < $product->rating ? ' text-warning' : ' text-muted' }} small"></i>
                                @endfor
                            </span>
                        </div>
                        <div class="d-flex mb-3">
                            <span class="product-info-label">Terjual:</span>
                            <span class="product-info-value">{{ $product->sold }} produk</span>
                        </div>
                        <div class="d-flex mb-3">
                            <span class="product-info-label">Status:</span>
                            <span class="product-info-value">
                                @if($product->stock > 0)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Stok Habis</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Description -->
                <div class="mb-4">
                    <h5>Deskripsi</h5>
                    <hr>
                    <p>{{ $product->short_description }}</p>
                    <div>
                        {!! $product->description !!}
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Product Statistics -->
        <div class="admin-card mt-4">
            <div class="admin-card-header">
                <h5 class="admin-card-title">Statistik Produk</h5>
            </div>
            <div class="admin-card-body">
                <div class="row">
                    <div class="col-md-3 text-center">
                        <h3>{{ $product->views }}</h3>
                        <p class="text-muted">Dilihat</p>
                    </div>
                    <div class="col-md-3 text-center">
                        <h3>{{ $product->sold }}</h3>
                        <p class="text-muted">Terjual</p>
                    </div>
                    <div class="col-md-3 text-center">
                        <h3>{{ $product->reviews_count }}</h3>
                        <p class="text-muted">Ulasan</p>
                    </div>
                    <div class="col-md-3 text-center">
                        <h3>{{ $product->wishlist_count }}</h3>
                        <p class="text-muted">Wishlist</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Reviews -->
<div class="admin-card mt-4">
    <div class="admin-card-header">
        <h5 class="admin-card-title">Ulasan Produk</h5>
    </div>
    <div class="admin-card-body p-0">
        @if(count($reviews) > 0)
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Pelanggan</th>
                            <th>Rating</th>
                            <th>Komentar</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reviews as $review)
                        <tr>
                            <td>{{ $review->user->name }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="me-1">{{ $review->rating }}</span>
                                    @for($i = 0; $i < 5; $i++)
                                        <i class="fas fa-star{{ $i < $review->rating ? ' text-warning' : ' text-muted' }} small"></i>
                                    @endfor
                                </div>
                            </td>
                            <td>{{ Str::limit($review->comment, 100) }}</td>
                            <td>{{ $review->created_at->format('d M Y, H:i') }}</td>
                            <td>
                                <a href="#" class="table-action text-danger" title="Hapus" 
                                   onclick="event.preventDefault(); if(confirm('Apakah Anda yakin ingin menghapus ulasan ini?')) document.getElementById('delete-review-{{ $review->id }}').submit();">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <form id="delete-review-{{ $review->id }}" action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center my-3">
                {{ $reviews->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <p class="mb-0">Belum ada ulasan untuk produk ini.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Change product main image
    function changeImage(src) {
        document.getElementById('main-product-image').src = src;
        
        // Update active class on thumbnails
        document.querySelectorAll('.product-thumbnail').forEach(thumbnail => {
            if (thumbnail.src === src) {
                thumbnail.classList.add('active');
            } else {
                thumbnail.classList.remove('active');
            }
        });
    }
</script>
@endsection