@extends('layouts.admin')

@section('title', 'Manajemen Kategori')

@section('content-title', 'Manajemen Kategori')

@section('styles')
<style>
    .category-image {
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
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Daftar Kategori</h1>
        <p class="text-muted">Kelola semua kategori produk toko Anda</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-danger">
        <i class="fas fa-plus me-1"></i> Tambah Kategori
    </a>
</div>

<div class="row">
    <!-- Filters -->
    <div class="col-md-12">
        <div class="admin-card filter-card">
            <div class="admin-card-body">
                <form action="{{ route('admin.categories.index') }}" method="GET" class="row g-3">
                    <div class="col-md-6">
                        <label for="search" class="form-label">Cari Kategori</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Cari nama kategori...">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> Filter
                        </button>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Categories Table -->
    <div class="col-md-12">
        <div class="admin-card">
            <div class="admin-card-body p-0">
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Gambar</th>
                                <th>Nama Kategori</th>
                                <th>Deskripsi</th>
                                <th>Jumlah Produk</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                            <tr>
                                <td>#{{ $category->id }}</td>
                                <td>
                                    <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" class="category-image">
                                </td>
                                <td>{{ $category->name }}</td>
                                <td>{{ Str::limit($category->description, 50) }}</td>
                                <td>{{ $category->products_count }}</td>
                                <td>
                                    <a href="{{ route('admin.categories.show', $category->id) }}" class="table-action" title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="table-action" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" class="table-action text-danger" title="Hapus" 
                                       onclick="event.preventDefault(); if(confirm('Apakah Anda yakin ingin menghapus kategori ini?')) document.getElementById('delete-category-{{ $category->id }}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <form id="delete-category-{{ $category->id }}" action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-none">
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
            {{ $categories->appends(request()->except('page'))->links() }}
        </div>
    </div>
</div>
@endsection