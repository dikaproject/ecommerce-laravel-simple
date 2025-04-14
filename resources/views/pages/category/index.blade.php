<!-- resources/views/pages/category/index.blade.php -->
@extends('layouts.app')

@section('title', 'Kategori - Izzi Craft')

@section('styles')
<style>
    .category-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
    }
    
    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    
    .category-image {
        height: 200px;
        object-fit: cover;
    }
    
    .category-banner {
        background-color: #f8f9fa;
        padding: 60px 0;
        margin-bottom: 40px;
    }
</style>
@endsection

@section('content')
<!-- Category Banner -->
<div class="category-banner">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-5 fw-bold mb-3">Kategori Produk</h1>
                <p class="lead">Temukan berbagai kategori produk craft dan buket untuk semua kebutuhan kreatif Anda. Dari aksesoris craft hingga bahan buket, semua tersedia dengan harga terjangkau.</p>
            </div>
            <div class="col-lg-6">
                <div class="text-center">
                    <img src="{{ asset('images/craft-supplier.png') }}" alt="Kategori Produk" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container pb-5">
    <div class="row g-4">
        @forelse($categories as $category)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card category-card h-100 border-0 shadow-sm">
                    <a href="{{ route('category.show', $category->slug) }}" class="text-decoration-none">
                        <img src="{{ asset($category->image) }}" class="card-img-top category-image" alt="{{ $category->name }}">
                        <div class="card-body text-center">
                            <h5 class="card-title text-dark">{{ $category->name }}</h5>
                            <p class="card-text text-muted small">{{ $category->products_count }} produk</p>
                        </div>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center p-5">
                    <i class="fas fa-info-circle fa-3x mb-3"></i>
                    <h4>Belum ada kategori</h4>
                    <p>Kategori produk belum tersedia saat ini.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- All Products Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2>Semua Produk Craft</h2>
            <p class="lead">Jelajahi semua produk craft kami tanpa filter kategori</p>
        </div>
        <div class="d-flex justify-content-center">
            <a href="{{ route('products.index') }}" class="btn btn-danger btn-lg px-4">
                Lihat Semua Produk
            </a>
        </div>
    </div>
</section>
@endsection