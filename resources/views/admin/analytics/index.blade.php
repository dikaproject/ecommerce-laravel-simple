@extends('layouts.admin')

@section('title', 'Analitik Penjualan')

@section('content-title', 'Analitik Penjualan')

@section('styles')
<style>
    /* Clean and simple styles */
    .card {
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 24px;
        border: none;
    }
    
    .card-header {
        background-color: #fff;
        border-bottom: 1px solid #f0f0f0;
        padding: 16px 20px;
        font-weight: 600;
    }
    
    .card-body {
        padding: 20px;
    }
    
    .stats-card {
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    /* Adding more bottom margin to the row containing stat cards */
    .stats-row {
        margin-bottom: 30px;
    }
    
    .stats-number {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 5px;
        color: #333;
    }
    
    .stats-label {
        color: #666;
        font-size: 14px;
        margin-bottom: 10px;
    }
    
    .stats-change {
        margin-top: auto;
        font-size: 13px;
        font-weight: 500;
        padding: 5px 10px;
        border-radius: 4px;
        display: inline-block;
    }
    
    .stats-change.positive {
        background-color: rgba(40, 167, 69, 0.1);
        color: #28a745;
    }
    
    .stats-change.negative {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }
    
    .chart-container {
        position: relative;
        height: 300px;
    }
    
    .product-item {
        padding: 12px;
        margin-bottom: 10px;
        border-radius: 6px;
        transition: all 0.2s;
        display: flex;
        align-items: center;
    }
    
    .product-item:hover {
        background-color: #f8f9fa;
    }
    
    .product-rank {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background-color: #f0f0f0;
        color: #333;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        margin-right: 12px;
    }
    
    .rank-1 {
        background-color: #ffd700;
        color: #333;
    }
    
    .rank-2 {
        background-color: #c0c0c0;
        color: #333;
    }
    
    .rank-3 {
        background-color: #cd7f32;
        color: #fff;
    }
    
    .product-img {
        width: 48px;
        height: 48px;
        object-fit: cover;
        border-radius: 6px;
        margin-right: 12px;
    }
    
    .product-details {
        flex-grow: 1;
    }
    
    .product-name {
        font-weight: 600;
        margin-bottom: 4px;
        font-size: 14px;
    }
    
    .product-meta {
        color: #6c757d;
        font-size: 12px;
    }
    
    .divider {
        margin: 0 5px;
    }
    
    .period-filter {
        margin-bottom: 24px;
        padding: 16px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .export-btn {
        background-color: #28a745;
        color: white;
        font-weight: 500;
        padding: 8px 16px;
        border-radius: 4px;
        border: none;
        transition: all 0.2s;
    }
    
    .export-btn:hover {
        background-color: #218838;
        color: white;
    }
    
    .chart-options {
        display: flex;
        justify-content: center;
        margin-bottom: 16px;
    }
    
    .chart-option {
        padding: 6px 12px;
        font-size: 13px;
        border: 1px solid #dee2e6;
        color: #6c757d;
        background-color: white;
        cursor: pointer;
    }
    
    .chart-option:first-child {
        border-radius: 4px 0 0 4px;
    }
    
    .chart-option:last-child {
        border-radius: 0 4px 4px 0;
    }
    
    .chart-option.active {
        background-color: #dc3545;
        color: white;
        border-color: #dc3545;
    }
    
    .table {
        width: 100%;
        font-size: 14px;
    }
    
    .table th {
        font-weight: 600;
        color: #333;
        border-top: none;
        background-color: #f8f9fa;
    }
    
    .table td, .table th {
        padding: 12px 16px;
        vertical-align: middle;
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Analitik Penjualan</h1>
    <a href="{{ route('admin.analytics.export') }}" class="export-btn">
        <i class="fas fa-file-excel me-2"></i>Ekspor Data
    </a>
</div>

<!-- Period Filter -->
<div class="period-filter">
    <form id="period-form" action="{{ route('admin.analytics') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-auto">
            <label for="period" class="me-2">Periode:</label>
            <select id="period" name="period" class="form-select form-select-sm" onchange="document.getElementById('period-form').submit()">
                <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Minggu Ini</option>
                <option value="month" {{ request('period') == 'month' || !request('period') ? 'selected' : '' }}>Bulan Ini</option>
                <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>Tahun Ini</option>
                <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Kustom</option>
            </select>
        </div>
        
        @if(request('period') == 'custom')
        <div class="col-auto">
            <input type="date" class="form-control form-control-sm" name="start_date" value="{{ request('start_date') }}">
        </div>
        <div class="col-auto">
            <span>sampai</span>
        </div>
        <div class="col-auto">
            <input type="date" class="form-control form-control-sm" name="end_date" value="{{ request('end_date') }}">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-sm btn-primary">Terapkan</button>
        </div>
        @endif
    </form>
</div>

<!-- Stats Overview -->
<div class="row stats-row">
    <div class="col-md-3 col-sm-6">
        <div class="card stats-card">
            <div class="card-body">
                <div class="stats-label">Total Transaksi</div>
                <div class="stats-number">{{ number_format($totalSales, 0, ',', '.') }}</div>
                @if($salesGrowth >= 0)
                <div class="stats-change positive">
                    <i class="fas fa-arrow-up me-1"></i>{{ $salesGrowth }}%
                </div>
                @else
                <div class="stats-change negative">
                    <i class="fas fa-arrow-down me-1"></i>{{ abs($salesGrowth) }}%
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card stats-card">
            <div class="card-body">
                <div class="stats-label">Total Pendapatan</div>
                <div class="stats-number">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</div>
                @if($revenueGrowth >= 0)
                <div class="stats-change positive">
                    <i class="fas fa-arrow-up me-1"></i>{{ $revenueGrowth }}%
                </div>
                @else
                <div class="stats-change negative">
                    <i class="fas fa-arrow-down me-1"></i>{{ abs($revenueGrowth) }}%
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card stats-card">
            <div class="card-body">
                <div class="stats-label">Nilai Rata-rata Pesanan</div>
                <div class="stats-number">Rp{{ number_format($averageOrderValue, 0, ',', '.') }}</div>
                @if($aovGrowth >= 0)
                <div class="stats-change positive">
                    <i class="fas fa-arrow-up me-1"></i>{{ $aovGrowth }}%
                </div>
                @else
                <div class="stats-change negative">
                    <i class="fas fa-arrow-down me-1"></i>{{ abs($aovGrowth) }}%
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card stats-card">
            <div class="card-body">
                <div class="stats-label">Jumlah Produk Terjual</div>
                <div class="stats-number">{{ number_format($totalUnitsSold, 0, ',', '.') }}</div>
                @if($unitsSoldGrowth >= 0)
                <div class="stats-change positive">
                    <i class="fas fa-arrow-up me-1"></i>{{ $unitsSoldGrowth }}%
                </div>
                @else
                <div class="stats-change negative">
                    <i class="fas fa-arrow-down me-1"></i>{{ abs($unitsSoldGrowth) }}%
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Tren Penjualan & Pendapatan</span>
                <div class="chart-options">
                    <button class="chart-option active" onclick="updateSalesChart('daily')">Harian</button>
                    <button class="chart-option" onclick="updateSalesChart('weekly')">Mingguan</button>
                    <button class="chart-option" onclick="updateSalesChart('monthly')">Bulanan</button>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">Distribusi Kategori</div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="categoriesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Products and Categories -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Produk Terlaris</span>
                <a href="{{ route('admin.products.index', ['sort' => 'popular']) }}" class="btn btn-sm btn-outline-secondary">Lihat Semua</a>
            </div>
            <div class="card-body">
                @foreach($topProducts as $index => $product)
                <div class="product-item">
                    <div class="product-rank rank-{{ $index + 1 }}">{{ $index + 1 }}</div>
                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="product-img">
                    <div class="product-details">
                        <div class="product-name">{{ $product->name }}</div>
                        <div class="product-meta">
                            <span>{{ number_format($product->sold, 0, ',', '.') }} terjual</span>
                            <span class="divider">•</span>
                            <span>Rp{{ number_format($product->price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Kategori Terpopuler</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Kategori</th>
                                <th>Terjual</th>
                                <th>Pendapatan</th>
                                <th>Pertumbuhan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topCategories as $category)
                            <tr>
                                <td>{{ $category->name }}</td>
                                <td>{{ number_format($category->total_sold, 0, ',', '.') }}</td>
                                <td>Rp{{ number_format($category->total_revenue, 0, ',', '.') }}</td>
                                <td>
                                    @if($category->growth >= 0)
                                        <span class="text-success">
                                            <i class="fas fa-arrow-up me-1"></i>{{ $category->growth }}%
                                        </span>
                                    @else
                                        <span class="text-danger">
                                            <i class="fas fa-arrow-down me-1"></i>{{ abs($category->growth) }}%
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Sales Trend Chart
    let salesChart;
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    
    function initSalesChart(data) {
        if (salesChart) {
            salesChart.destroy();
        }
        
        salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: 'Penjualan',
                        data: data.sales,
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.05)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3,
                        pointRadius: 3,
                        pointHoverRadius: 4
                    },
                    {
                        label: 'Pendapatan (Rp)',
                        data: data.revenue,
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.05)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3,
                        yAxisID: 'y1',
                        pointRadius: 3,
                        pointHoverRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                        },
                        title: {
                            display: true,
                            text: 'Jumlah Pesanan'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                        }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false,
                        },
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return (value / 1000000).toFixed(1) + ' Jt';
                                } else if (value >= 1000) {
                                    return (value / 1000).toFixed(0) + ' Rb';
                                }
                                return value;
                            }
                        },
                        title: {
                            display: true,
                            text: 'Pendapatan (Rp)'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    },
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            boxWidth: 12,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }
    
    function updateSalesChart(period, clickEvent = null) {
        // Update active button
        document.querySelectorAll('.chart-option').forEach(btn => {
            btn.classList.remove('active');
            
            // If we're not handling a click event, check if this button matches the period
            if (!clickEvent && btn.textContent.trim() === periodToButtonText(period)) {
                btn.classList.add('active');
            }
        });
        
        // If this was triggered by a click event, use the clicked element
        if (clickEvent) {
            clickEvent.target.classList.add('active');
        }
        
        // Fetch data for the selected period
        fetch(`{{ route('admin.analytics.sales-data') }}?period=${period}`)
            .then(response => response.json())
            .then(data => {
                initSalesChart(data);
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
    
    // Helper function to map period to button text
    function periodToButtonText(period) {
        switch(period) {
            case 'daily': return 'Harian';
            case 'weekly': return 'Mingguan';
            case 'monthly': return 'Bulanan';
            default: return '';
        }
    }
    
    // Initialize chart functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Add click event listeners to chart option buttons
        document.querySelectorAll('.chart-option').forEach(btn => {
            // Extract period from the original onclick
            const originalOnClick = btn.getAttribute('onclick');
            let period = '';
            
            if (originalOnClick) {
                const match = originalOnClick.match(/'([^']*)'/);
                if (match && match[1]) {
                    period = match[1];
                }
                // Remove original onclick to avoid duplicate execution
                btn.removeAttribute('onclick');
            }
            
            // Add new event listener
            btn.addEventListener('click', function(e) {
                updateSalesChart(period, e);
            });
        });
        
        // Force load daily data immediately after page load
        setTimeout(() => {
            // Initialize the chart with daily data on page load
            updateSalesChart('daily');
        }, 300);
    });
    
    // Categories Pie Chart
    const categoriesCtx = document.getElementById('categoriesChart').getContext('2d');
    new Chart(categoriesCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($categoriesChartData['labels']) !!},
            datasets: [{
                data: {!! json_encode($categoriesChartData['data']) !!},
                backgroundColor: [
                    '#dc3545', '#fd7e14', '#ffc107', '#28a745', '#20c997',
                    '#17a2b8', '#6610f2'
                ],
                borderWidth: 1,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        padding: 10,
                        font: {
                            size: 12
                        }
                    }
                }
            }
        }
    });
</script>
@endsection
```
