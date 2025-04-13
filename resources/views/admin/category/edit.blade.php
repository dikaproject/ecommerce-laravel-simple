@extends('layouts.admin')

@section('title', 'Edit Kategori')

@section('content-title', 'Edit Kategori')

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
            <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Kategori</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit {{ $category->name }}</li>
        </ol>
    </nav>
    <div>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-header">
        <h5 class="admin-card-title">Edit Kategori</h5>
    </div>
    <div class="admin-card-body">
        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row">
                <!-- Kolom Kiri -->
                <div class="col-md-6">
                    <!-- Nama Kategori -->
                    <div class="mb-3">
                        <label for="name" class="form-label required-label">Nama Kategori</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $category->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Slug -->
                    <div class="mb-3">
                        <label for="slug" class="form-label required-label">Slug</label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                               id="slug" name="slug" value="{{ old('slug', $category->slug) }}" required>
                        <small class="form-text text-muted">Slug harus unik, gunakan huruf kecil dan tanda strip.</small>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Deskripsi -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4">{{ old('description', $category->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Kolom Kanan -->
                <div class="col-md-6">
                    <!-- Gambar Kategori -->
                    <div class="mb-3">
                        <label for="image" class="form-label">Gambar Kategori</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" 
                               id="image" name="image" accept="image/*" onchange="previewImage(event)">
                        <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah gambar.</small>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        <div class="mt-2">
                            <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" class="image-preview" id="image-preview">
                        </div>
                    </div>
                
                </div>
            </div>
            
            <div class="d-flex justify-content-end mt-3">
                <button type="button" class="btn btn-secondary me-2" onclick="window.location.href='{{ route('admin.categories.index') }}'">Batal</button>
                <button type="submit" class="btn btn-danger">Simpan Perubahan</button>
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
        }
        reader.readAsDataURL(event.target.files[0]);
    }
    
    // Auto-generate slug from name
    document.getElementById('name').addEventListener('keyup', function() {
        let slug = this.value.toLowerCase()
            .replace(/[^\w\s-]/g, '')   // Remove special characters
            .replace(/\s+/g, '-')       // Replace spaces with hyphens
            .replace(/--+/g, '-');      // Replace multiple hyphens with single hyphen
        
        document.getElementById('slug').value = slug;
    });
</script>
@endsection