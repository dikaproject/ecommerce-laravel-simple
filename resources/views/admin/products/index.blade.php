<!-- resources/views/pages/admin/products/index.blade.php -->
@extends('layouts.admin')

@section('title', 'Manajemen Produk')

@section('content-title', 'Manajemen Produk')

@section('styles')
<style>
    .product-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 5px;
    }
    
    .filter-card {
        margin-bottom: 1.5rem;
    }
    
    .status-active {
        background-color: #d4edda;
        color: #155724;
    }
    
    .status-inactive {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    .stock-status {
        font-weight: 500;
        font-size: 0.85rem;
    }
    
    .stock-normal {
        color: #155724;
    }
    
    .stock-warning {
        color: #856404;
    }
    
    .stock-danger {
        color: #721c24;
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Daftar Produk</h1>
        <p class="text-muted">Kelola semua produk toko Anda</p>
    </div>
    <a href="{{ route('admin.products.create') }}" class="btn btn-danger">
        <i class="fas fa-plus me-1"></i> Tambah Produk
    </a>
</div>

<div class="row">
    <!-- Filters -->
    <div class="col-md-12">
        <div class="admin-card filter-card">
            <div class="admin-card-body">
                <form action="{{ route('admin.products.index') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Cari Produk</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Cari nama produk...">
                    </div>
                    <div class="col-md-3">
                        <label for="category" class="form-label">Kategori</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="stock" class="form-label">Status Stok</label>
                        <select class="form-select" id="stock" name="stock">
                            <option value="">Semua</option>
                            <option value="in" {{ request('stock') == 'in' ? 'selected' : '' }}>Tersedia</option>
                            <option value="low" {{ request('stock') == 'low' ? 'selected' : '' }}>Stok Menipis</option>
                            <option value="out" {{ request('stock') == 'out' ? 'selected' : '' }}>Habis</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> Filter
                        </button>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Products Table -->
    <div class="col-md-12">
        <div class="admin-card">
            <div class="admin-card-body p-0">
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Gambar</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Rating</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            <tr>
                                <td>#{{ $product->id }}</td>
                                <td>
                                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="product-image">
                                </td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category->name }}</td>
                                <td>Rp{{ number_format($product->price, 0, ',', '.') }}</td>
                                <td>
                                    @if($product->stock > 10)
                                        <span class="stock-status stock-normal">{{ $product->stock }}</span>
                                    @elseif($product->stock > 0)
                                        <span class="stock-status stock-warning">{{ $product->stock }}</span>
                                    @else
                                        <span class="stock-status stock-danger">{{ $product->stock }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="me-1">{{ number_format($product->rating, 1) }}</span>
                                        <i class="fas fa-star text-warning small"></i>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('admin.products.show', $product->id) }}" class="table-action" title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="table-action" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" class="table-action text-danger" title="Hapus" 
                                       onclick="event.preventDefault(); if(confirm('Apakah Anda yakin ingin menghapus produk ini?')) document.getElementById('delete-product-{{ $product->id }}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <form id="delete-product-{{ $product->id }}" action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $products->appends(request()->except('page'))->links() }}
        </div>
    </div>
</div>
@endsection