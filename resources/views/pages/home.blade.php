<!-- resources/views/pages/home.blade.php -->
@extends('layouts.app')

@section('title', 'Izzi Craft - Toko Kebutuhan Craft dan Buket Online')

@section('styles')
<style>
    .hero-section {
        background-color: #f8f9fa;
        padding: 60px 0 60px; /* Mengurangi bottom padding */
        margin-bottom: 20px; /* Menambahkan margin bottom untuk jarak dengan section berikutnya */
        overflow: hidden; /* Menghindari konten menembus section */
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

    /* Image stacking effect styles - improved */
    .stacked-images-container {
        position: relative;
        width: 100%;
        height: 380px; /* Increased height to accommodate larger square images */
        margin: 0 auto;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    .stacked-image {
        position: absolute;
        width: 85%; /* Slightly increased from 80% */
        height: 85%; /* Equal to width for 1:1 aspect ratio */
        object-fit: cover;
        border-radius: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .stacked-image-1 {
        top: 0;
        left: 0;
        z-index: 1;
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }
    
    .stacked-image-2 {
        top: 30px; /* Adjusted for larger images */
        left: 30px; /* Adjusted for larger images */
        z-index: 2;
    }
    
    @media (max-width: 992px) {
        .stacked-images-container {
            height: 320px; /* Adjusted for medium screens */
            margin-bottom: 0;
        }
        
        .stacked-image {
            width: 80%; /* Slightly reduced for medium screens */
            height: 80%; /* Equal to width for 1:1 aspect ratio */
        }
        
        .stacked-image-2 {
            top: 25px;
            left: 25px;
        }
    }
    
    @media (max-width: 768px) {
        .stacked-images-container {
            height: 280px; /* Adjusted for smaller screens */
        }
        
        .stacked-image {
            width: 90%;
            height: 90%; /* Equal to width for 1:1 aspect ratio */
        }
        
        .stacked-image-2 {
            top: 20px;
            left: 20px;
        }
    }

    /* New modern craft inspiration styles */
    .craft-inspiration-card {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        height: 100%;
        background-color: white;
    }
    
    .craft-inspiration-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    
    .craft-inspiration-img-container {
        position: relative;
        width: 100%;
        padding-top: 75%; /* 4:3 Aspect Ratio */
        overflow: hidden;
    }
    
    .craft-inspiration-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .craft-inspiration-card:hover .craft-inspiration-img {
        transform: scale(1.05);
    }
    
    .craft-inspiration-content {
        padding: 20px;
    }
    
    .craft-inspiration-content h5 {
        font-weight: 600;
        margin-bottom: 10px;
        color: #333;
    }
    
    .craft-inspiration-content p {
        color: #666;
        font-size: 0.95rem;
        margin-bottom: 0;
    }
    
    @media (max-width: 768px) {
        .craft-inspiration-card {
            margin-bottom: 20px;
        }
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
                <div class="stacked-images-container">
                    <img src="{{ asset('images/craft-supplier.png') }}" alt="Craft Supplies" class="stacked-image stacked-image-1">
                    <img src="{{ asset('images/craft-supplier.png') }}" alt="Craft Supplies" class="stacked-image stacked-image-2">
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

<!-- Craft Collections -->
<section class="py-5">
    <div class="container">
        <h2 class="section-title">Inspirasi Craft</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="craft-inspiration-card">
                    <div class="craft-inspiration-img-container">
                        <img src="{{ asset('images/inspiration-1.jpg') }}" alt="Craft Inspiration" class="craft-inspiration-img">
                    </div>
                    <div class="craft-inspiration-content">
                        <h5>Buket Bunga Handmade</h5>
                        <p>Buat buket bunga custom dengan material pilihan untuk berbagai acara spesial.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="craft-inspiration-card">
                    <div class="craft-inspiration-img-container">
                        <img src="{{ asset('images/inspiration-2.jpg') }}" alt="Craft Inspiration" class="craft-inspiration-img">
                    </div>
                    <div class="craft-inspiration-content">
                        <h5>DIY Gift Box</h5>
                        <p>Kreasi kotak hadiah unik dan personal untuk orang tersayang.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="craft-inspiration-card">
                    <div class="craft-inspiration-img-container">
                        <img src="{{ asset('images/inspiration-3.webp') }}" alt="Craft Inspiration" class="craft-inspiration-img">
                    </div>
                    <div class="craft-inspiration-content">
                        <h5>Scrapbook Memories</h5>
                        <p>Abadikan momen berharga dengan scrapbook kreatif dan penuh makna.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection