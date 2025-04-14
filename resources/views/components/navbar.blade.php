<header class="sticky-top">
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm" style="background: linear-gradient(135deg, #e62c3b, #c82333);">
        <div class="container py-1">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <img src="{{ asset('images/logo-izzicraft.png') }}" alt="Logo" width="36" height="36" class="d-inline-block me-2">
                <span class="fw-bold">Izzi Craft</span>
            </a>
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" 
                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-0">
                    <li class="nav-item">
                        <a class="nav-link px-3" href="{{ route('home') }}">Beranda</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle px-3" href="#" id="navbarDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            Kategori
                        </a>
                        <ul class="dropdown-menu shadow-sm border-0 rounded-3" aria-labelledby="navbarDropdown">
                            @foreach($navbarCategories as $category)
                                <li><a class="dropdown-item py-2" href="{{ route('category.show', $category->slug) }}">{{ $category->name }}</a></li>
                            @endforeach
                            <li><hr class="dropdown-divider my-1"></li>
                            <li><a class="dropdown-item py-2" href="{{ route('category.index') }}">Semua Kategori</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('products.index') }}">Semua Produk</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="{{ route('cart.index') }}">
                            <i class="fas fa-shopping-cart me-1"></i>Keranjang
                        </a>
                    </li>
                </ul>
                
                <form class="d-flex me-3" action="{{ route('products.search') }}" method="GET">
                    <div class="input-group">
                        <input class="form-control border-0 rounded-pill rounded-end-0 py-2 ps-3" type="search" name="search" placeholder="Cari produk..." aria-label="Search">
                        <button class="btn btn-light border-0 rounded-pill rounded-start-0 px-3" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                
                <div class="d-flex">
                    @auth
                        <div class="dropdown">
                            <button class="btn btn-light border-0 rounded-pill shadow-sm dropdown-toggle" type="button" id="userDropdown" 
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-1"></i> {{ Auth::user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item py-2" href="{{ route('profile') }}"><i class="fas fa-user-circle me-2"></i>Profil</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('orders.index') }}"><i class="fas fa-shopping-bag me-2"></i>Pesanan Saya</a></li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button class="dropdown-item py-2" type="submit"><i class="fas fa-sign-out-alt me-2"></i>Keluar</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-light border-0 rounded-pill shadow-sm">
                            <i class="fas fa-sign-in-alt me-1"></i> Masuk
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
</header>