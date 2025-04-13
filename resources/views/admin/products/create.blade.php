<!-- resources/views/pages/admin/products/create.blade.php -->
@extends('layouts.admin')

@section('title', 'Tambah Produk Baru')

@section('content-title', 'Tambah Produk Baru')

@section('styles')
<style>
    .form-label {
        font-weight: 500;
    }
    
    .image-preview {
        width: 150px;
        height: 150px;
        object-fit: contain;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 5px;
        margin-top: 10px;
        display: none;
    }
    
    .required-label::after {
        content: " *";
        color: #dc3545;
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Produk</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tambah Produk</li>
        </ol>
    </nav>
    <div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-header">
        <h5 class="admin-card-title">Tambah Produk Baru</h5>
    </div>
    <div class="admin-card-body">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <!-- Kolom Kiri -->
                <div class="col-md-6">
                    <!-- Nama Produk -->
                    <div class="mb-3">
                        <label for="name" class="form-label required-label">Nama Produk</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Kategori -->
                    <div class="mb-3">
                        <label for="category_id" class="form-label required-label">Kategori</label>
                        <select class="form-select @error('category_id') is-invalid @enderror" 
                                id="category_id" name="category_id" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Harga -->
                    <div class="mb-3">
                        <label for="price" class="form-label required-label">Harga (Rp)</label>
                        <input type="number" class="form-control @error('price') is-invalid @enderror" 
                               id="price" name="price" value="{{ old('price', 0) }}" min="0" required>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Stok -->
                    <div class="mb-3">
                        <label for="stock" class="form-label required-label">Stok</label>
                        <input type="number" class="form-control @error('stock') is-invalid @enderror" 
                               id="stock" name="stock" value="{{ old('stock', 0) }}" min="0" required>
                        @error('stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Kolom Kanan -->
                <div class="col-md-6">
                    <!-- Gambar Produk -->
                    <div class="mb-3">
                        <label for="image" class="form-label required-label">Gambar Produk</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" 
                               id="image" name="image" accept="image/*" onchange="previewImage(event)" required>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        <div class="mt-2">
                            <img src="" alt="Preview" class="image-preview" id="image-preview">
                        </div>
                    </div>
                    
                    <!-- Rating (default) -->
                    <div class="mb-3">
                        <label for="rating" class="form-label">Rating</label>
                        <div class="input-group">
                            <input type="text" class="form-control" value="0.0" readonly>
                            <span class="input-group-text bg-warning text-dark">
                                <i class="fas fa-star"></i>
                            </span>
                        </div>
                        <small class="form-text text-muted">Rating awal diatur 0 dan akan diperbarui berdasarkan ulasan.</small>
                    </div>
                    
                    <!-- Deskripsi -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end mt-3">
                <button type="button" class="btn btn-secondary me-2" onclick="window.location.href='{{ route('admin.products.index') }}'">Batal</button>
                <button type="submit" class="btn btn-danger">Simpan Produk</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Preview image before upload
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const imagePreview = document.getElementById('image-preview');
            imagePreview.src = reader.result;
            imagePreview.style.display = 'block';
        }
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection