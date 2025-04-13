<!-- resources/views/pages/home.blade.php -->
@extends('layouts.app')

@section('title', 'Izzi Craft - Toko Kebutuhan Craft dan Buket Online')

@section('styles')
<style>
    .hero-section {
        background-color: #f8f9fa;
        padding: 60px 0;
    }
    
    .section-title {
        position: relative;
        padding-bottom: 15px;
        margin-bottom: 30px;
    }
    
    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 60px;
        height: 3px;
        background-color: #dc3545;
    }
    
    .new-label {
        background-color: #ffc107;
        color: #000;
        font-size: 0.8rem;
        padding: 0.2rem 0.5rem;
        border-radius: 0.25rem;
    }
    
    .popular-label {
        display: inline-flex;
        align-items: center;
    }
    
    .popular-label i {
        color: #ffc107;
        margin-left: 0.25rem;
    }
    
    .craft-collections img {
        border-radius: 10px;
        margin-bottom: 15px;
    }
    
    .promo-banner {
        border-radius: 10px;
        overflow: hidden;
        position: relative;
        margin-bottom: 30px;
    }
    
    .promo-banner img {
        width: 100%;
        transition: transform 0.3s;
    }
    
    .promo-banner:hover img {
        transform: scale(1.05);
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-5 fw-bold mb-3">Semua Kebutuhan Craft & Buket Ada di Sini!</h1>
                <p class="lead mb-4">
                    Butuh aksesoris craft atau bahan buket? Kami menyediakan berbagai pilihan untuk mendukung ide kreatifmu. 
                    Belanja mudah, harga terjangkau, dan siap kirim ke rumahmu. Yuk, wujudkan karyamu sekarang!
                </p>
                <a href="{{ route('products.index') }}" class="btn btn-danger btn-lg px-4">
                    Belanja Sekarang
                </a>
            </div>
            <div class="col-lg-6">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <img src="{{ asset('images/craft-supplies-1.jpg') }}" alt="Craft Supplies" class="img-fluid rounded">
                    </div>
                    <div class="col-md-6 mb-3">
                        <img src="{{ asset('images/craft-supplies-2.jpg') }}" alt="Craft Supplies" class="img-fluid rounded">
                    </div>
                    <div class="col-md-12">
                        <img src="{{ asset('images/craft-supplies-3.jpg') }}" alt="Craft Supplies" class="img-fluid rounded">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-5">
    <div class="container">
        <h2 class="section-title">Kategori Populer</h2>
        <div class="row g-4">
            @foreach($categories as $category)
            <div class="col-6 col-md-3">
                <a href="{{ route('category.show', $category->slug) }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm">
                        <img src="{{ asset($category->image) }}" class="card-img-top" alt="{{ $category->name }}">
                        <div class="card-body text-center">
                            <h5 class="card-title text-dark">{{ $category->name }}</h5>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- New Products Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title mb-0">Produk Terbaru <span class="new-label ms-2">NEW</span></h2>
            <a href="{{ route('products.index') }}" class="btn btn-outline-danger">
                Lihat Semua
            </a>
        </div>
        
        <div class="row g-4">
            @foreach ($newProducts as $product)
                <div class="col-6 col-md-3">
                    @include('components.product-card', ['product' => $product])
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Popular Products Section -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title mb-0">
                <span class="popular-label">Populer <i class="fas fa-fire"></i></span>
            </h2>
            <a href="{{ route('products.index', ['sort' => 'popular']) }}" class="btn btn-outline-danger">
                Lihat Semua
            </a>
        </div>
        
        <div class="row g-4">
            @foreach ($popularProducts as $product)
                <div class="col-6 col-md-3">
                    @include('components.product-card', ['product' => $product])
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Promo Banners -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="promo-banner">
                    <img src="{{ asset('images/promo-banner-1.jpg') }}" alt="Special Offer" class="img-fluid">
                </div>
            </div>
            <div class="col-md-6">
                <div class="promo-banner">
                    <img src="{{ asset('images/promo-banner-2.jpg') }}" alt="Special Offer" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Craft Collections -->
<section class="py-5">
    <div class="container">
        <h2 class="section-title">Inspirasi Craft</h2>
        <div class="row craft-collections">
            <div class="col-md-4 mb-4">
                <img src="{{ asset('images/inspiration-1.jpg') }}" alt="Craft Inspiration" class="img-fluid">
                <h5>Buket Bunga Handmade</h5>
                <p>Buat buket bunga custom dengan material pilihan untuk berbagai acara spesial.</p>
            </div>
            <div class="col-md-4 mb-4">
                <img src="{{ asset('images/inspiration-2.jpg') }}" alt="Craft Inspiration" class="img-fluid">
                <h5>DIY Gift Box</h5>
                <p>Kreasi kotak hadiah unik dan personal untuk orang tersayang.</p>
            </div>
            <div class="col-md-4 mb-4">
                <img src="{{ asset('images/inspiration-3.jpg') }}" alt="Craft Inspiration" class="img-fluid">
                <h5>Scrapbook Memories</h5>
                <p>Abadikan momen berharga dengan scrapbook kreatif dan penuh makna.</p>
            </div>
        </div>
    </div>
</section>
@endsection