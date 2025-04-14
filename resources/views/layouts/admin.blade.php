<!-- resources/views/layouts/admin.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Izzi Craft</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Admin CSS -->
    <style>
        :root {
            --primary-color: #dc3545;
            --primary-dark: #c82333;
            --primary-light: #f8d7da;
            --secondary-color: #6c757d;
            --dark-color: #343a40;
            --light-color: #f8f9fa;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
        }
        
        /* Sidebar styles */
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background-color: var(--dark-color);
            color: white;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            transition: all 0.3s;
        }
        
        .sidebar-collapsed {
            width: 70px;
        }
        
        .sidebar-header {
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .sidebar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .sidebar-brand img {
            width: 32px;
            height: 32px;
            margin-right: 10px;
        }
        
        .toggle-sidebar {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 1.2rem;
        }
        
        .sidebar-menu {
            padding: 1rem 0;
        }
        
        .menu-item {
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .menu-item:hover, .menu-item.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            border-left: 4px solid var(--primary-color);
        }
        
        .menu-item i {
            width: 24px;
            margin-right: 10px;
            text-align: center;
        }
        
        .menu-text {
            white-space: nowrap;
            overflow: hidden;
        }
        
        /* Main content styles */
        .main-content {
            flex: 1;
            margin-left: 250px;
            transition: all 0.3s;
        }
        
        .main-content-expanded {
            margin-left: 70px;
        }
        
        .content-header {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .content-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }
        
        .user-dropdown img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .content-body {
            padding: 1.5rem;
        }
        
        /* Card styles */
        .admin-card {
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }
        
        .admin-card-header {
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .admin-card-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0;
        }
        
        .admin-card-body {
            padding: 1rem;
        }
        
        /* Dashboard stats */
        .stat-card {
            background-color: white;
            border-left: 4px solid var(--primary-color);
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: var(--primary-light);
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-right: 1rem;
        }
        
        .stat-info {
            flex: 1;
        }
        
        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
            line-height: 1.2;
        }
        
        .stat-label {
            color: var(--secondary-color);
            margin: 0;
        }
        
        .stat-change {
            margin-left: 1rem;
            font-size: 0.9rem;
        }
        
        .stat-change.positive {
            color: #28a745;
        }
        
        .stat-change.negative {
            color: var(--primary-color);
        }
        
        /* Table styles */
        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .admin-table th, .admin-table td {
            padding: 0.75rem;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .admin-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            text-align: left;
        }
        
        .admin-table tr:hover {
            background-color: #f8f9fa;
        }
        
        .table-action {
            color: var(--secondary-color);
            margin-right: 0.5rem;
            font-size: 0.9rem;
        }
        
        .table-action:hover {
            color: var(--primary-color);
        }
        
        /* Form styles */
        .admin-form label {
            font-weight: 500;
        }
        
        .admin-form .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
        }
        
        /* Badges */
        .status-badge {
            padding: 0.35em 0.65em;
            border-radius: 0.25rem;
            font-size: 0.75em;
            font-weight: 700;
            text-transform: uppercase;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-processing {
            background-color: #cce5ff;
            color: #004085;
        }
        
        .status-shipped {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .status-delivered {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
            }
            
            .sidebar-collapsed {
                width: 0;
                overflow: hidden;
            }
            
            .main-content {
                margin-left: 70px;
            }
            
            .main-content-expanded {
                margin-left: 0;
            }
            
            .menu-text {
                display: none;
            }
        }
    </style>
    
    <!-- Additional styles -->
    @yield('styles')
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
                    <img src="{{ asset('images/logo-izzicraft.png') }}" alt="Logo">
                    <span class="menu-text">Izzi Craft</span>
                </a>
                <button type="button" class="toggle-sidebar" id="toggleSidebar">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <div class="sidebar-menu">
                <a href="{{ route('admin.dashboard') }}" class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span class="menu-text">Dashboard</span>
                </a>
                <a href="{{ route('admin.products.index') }}" class="menu-item {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i>
                    <span class="menu-text">Produk</span>
                </a>
                <a href="{{ route('admin.categories.index') }}" class="menu-item {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
                    <i class="fas fa-tags"></i>
                    <span class="menu-text">Kategori</span>
                </a>
                <a href="{{ route('admin.orders.index') }}" class="menu-item {{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="menu-text">Pesanan</span>
                </a>
                <a href="{{ route('admin.analytics') }}" class="menu-item {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i>
                    <span class="menu-text">Analitik</span>
                </a>
                <a href="{{ route('admin.customers.index') }}" class="menu-item {{ request()->routeIs('admin.customers*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span class="menu-text">Pelanggan</span>
                </a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <div class="content-header">
                <div class="content-title">@yield('content-title', 'Dashboard')</div>
                
                <div class="d-flex align-items-center">
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ Auth::user()->avatar ? asset('storage/avatars/' . Auth::user()->avatar) : asset('images/admin-avatar.jpg') }}" alt="{{ Auth::user()->name }}" width="32" height="32" style="border-radius: 50%; object-fit: cover;">
                            <span class="ms-2 d-none d-md-inline">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="{{ route('admin.profile') }}">Profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Keluar</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="content-body">
                @yield('content')
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Chart.js (for analytics) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Admin JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleSidebar = document.getElementById('toggleSidebar');
            
            toggleSidebar.addEventListener('click', function() {
                sidebar.classList.toggle('sidebar-collapsed');
                mainContent.classList.toggle('main-content-expanded');
            });
        });
    </script>
    
    <!-- Additional scripts -->
    @yield('scripts')
</body>
</html>