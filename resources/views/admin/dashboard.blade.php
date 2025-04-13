<!-- resources/views/pages/admin/dashboard.blade.php -->
@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content-title', 'Dashboard')

@section('content')
<!-- Stats Cards -->
<div class="row">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-info">
                <h2 class="stat-value">{{ $totalOrders }}</h2>
                <p class="stat-label">Total Pesanan</p>
            </div>
            <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i> {{ $orderGrowth }}%
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-info">
                <h2 class="stat-value">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</h2>
                <p class="stat-label">Total Pendapatan</p>
            </div>
            <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i> {{ $revenueGrowth }}%
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h2 class="stat-value">{{ $totalCustomers }}</h2>
                <p class="stat-label">Total Pelanggan</p>
            </div>
            <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i> {{ $customerGrowth }}%
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-info">
                <h2 class="stat-value">{{ $totalProducts }}</h2>
                <p class="stat-label">Total Produk</p>
            </div>
            <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i> {{ $productGrowth }}%
            </div>
        </div>
    </div>
</div>

<!-- Sales Chart -->
<div class="row">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title">Grafik Penjualan</h5>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="updateChart('weekly')">Mingguan</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary active" onclick="updateChart('monthly')">Bulanan</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="updateChart('yearly')">Tahunan</button>
                </div>
            </div>
            <div class="admin-card-body">
                <canvas id="salesChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title">Produk Terlaris</h5>
                <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-danger">Lihat Semua</a>
            </div>
            <div class="admin-card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($topProducts as $product)
                    <li class="list-group-item d-flex align-items-center">
                        <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" width="40" height="40" class="rounded me-3">
                        <div class="flex-grow-1">
                            <h6 class="mb-0">{{ $product->name }}</h6>
                            <small class="text-muted">{{ $product->sold }} terjual</small>
                        </div>
                        <span class="text-danger fw-bold">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders & Stock Alert -->
<div class="row">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title">Pesanan Terbaru</h5>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-danger">Lihat Semua</a>
            </div>
            <div class="admin-card-body p-0">
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID Pesanan</th>
                                <th>Pelanggan</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentOrders as $order)
                            <tr>
                                <td>#{{ $order->order_number }}</td>
                                <td>{{ $order->user->name }}</td>
                                <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                                <td>Rp{{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                <td>
                                    @if($order->status == 'pending')
                                        <span class="status-badge status-pending">Pending</span>
                                    @elseif($order->status == 'processing')
                                        <span class="status-badge status-processing">Diproses</span>
                                    @elseif($order->status == 'shipped')
                                        <span class="status-badge status-shipped">Dikirim</span>
                                    @elseif($order->status == 'delivered')
                                        <span class="status-badge status-delivered">Diterima</span>
                                    @elseif($order->status == 'cancelled')
                                        <span class="status-badge status-cancelled">Dibatalkan</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="table-action" title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.orders.edit', $order->id) }}" class="table-action" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="admin-card">
            <div class="admin-card-header">
                <h5 class="admin-card-title">Stok Menipis</h5>
                <a href="{{ route('admin.products.index', ['stock' => 'low']) }}" class="btn btn-sm btn-outline-danger">Lihat Semua</a>
            </div>
            <div class="admin-card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($lowStockProducts as $product)
                    <li class="list-group-item d-flex align-items-center">
                        <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" width="40" height="40" class="rounded me-3">
                        <div class="flex-grow-1">
                            <h6 class="mb-0">{{ $product->name }}</h6>
                            <small class="text-danger">Stok: {{ $product->stock }}</small>
                        </div>
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-outline-danger">
                            Update
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Sales Chart
    const ctx = document.getElementById('salesChart').getContext('2d');
    let salesChart;
    
    function initChart(data) {
        salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Penjualan',
                    data: data.sales,
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    borderColor: '#dc3545',
                    tension: 0.4,
                    pointBackgroundColor: '#dc3545',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true
                }, {
                    label: 'Pendapatan',
                    data: data.revenue,
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    borderColor: '#28a745',
                    tension: 0.4,
                    pointBackgroundColor: '#28a745',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.dataset.label === 'Pendapatan') {
                                    label += 'Rp' + context.raw.toLocaleString('id-ID');
                                } else {
                                    label += context.raw;
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    }
                }
            }
        });
    }
    
    function updateChart(period) {
        // Update active button
        document.querySelectorAll('.btn-group .btn').forEach(btn => {
            btn.classList.remove('active');
        });
        event.target.classList.add('active');
        
        // Fetch data for the selected period
        fetch(`{{ route('admin.sales.data') }}?period=${period}`)
            .then(response => response.json())
            .then(data => {
                if (salesChart) {
                    salesChart.destroy();
                }
                initChart(data);
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
    
    // Initialize chart with monthly data
    document.addEventListener('DOMContentLoaded', function() {
        fetch('{{ route('admin.sales.data') }}?period=monthly')
            .then(response => response.json())
            .then(data => {
                initChart(data);
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });
</script>
@endsection