@extends('layouts.admin')

@section('title', 'Product Analytics')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.css">
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Product Analytics</h1>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-calendar"></i> Time Period
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#">Last 7 Days</a></li>
                <li><a class="dropdown-item" href="#">Last 30 Days</a></li>
                <li><a class="dropdown-item" href="#">Last 90 Days</a></li>
                <li><a class="dropdown-item" href="#">Last Year</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#">Custom Range</a></li>
            </ul>
        </div>
    </div>

    <!-- Product Overview Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Products
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($metrics['total_products']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-box fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Products
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($metrics['active_products']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Low Stock Items
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($metrics['low_stock']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-exclamation-triangle fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Out of Stock
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($metrics['out_of_stock']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-x-circle fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Top Selling Products Chart -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-bar-chart me-1"></i>
                    Top Selling Products
                </div>
                <div class="card-body">
                    <canvas id="topProductsChart" width="100%" height="50"></canvas>
                </div>
            </div>
        </div>

        <!-- Sales by Category Chart -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-pie-chart me-1"></i>
                    Sales by Category
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" width="100%" height="50"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Performance Table -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-table me-1"></i>
            Product Performance
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Units Sold</th>
                            <th>Revenue</th>
                            <th>Avg. Rating</th>
                            <th>Stock Level</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productPerformance as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category->name }}</td>
                                <td>{{ number_format($product->units_sold) }}</td>
                                <td>${{ number_format($product->revenue, 2) }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        {{ number_format($product->avg_rating, 1) }}
                                        <i class="bi bi-star-fill text-warning ms-1"></i>
                                    </div>
                                </td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar {{ $product->stock_level < 20 ? 'bg-danger' : ($product->stock_level < 50 ? 'bg-warning' : 'bg-success') }}"
                                             role="progressbar"
                                             style="width: {{ $product->stock_level }}%"
                                             aria-valuenow="{{ $product->stock_level }}"
                                             aria-valuemin="0"
                                             aria-valuemax="100">{{ $product->stock_level }}%</div>
                                    </div>
                                </td>
                                <td>
                                    @if($product->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($product->status === 'out_of_stock')
                                        <span class="badge bg-danger">Out of Stock</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Category Performance -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-diagram-3 me-1"></i>
            Category Performance
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Total Products</th>
                            <th>Active Products</th>
                            <th>Total Sales</th>
                            <th>Revenue</th>
                            <th>Avg. Rating</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categoryPerformance as $category)
                            <tr>
                                <td>{{ $category->name }}</td>
                                <td>{{ number_format($category->total_products) }}</td>
                                <td>{{ number_format($category->active_products) }}</td>
                                <td>{{ number_format($category->total_sales) }}</td>
                                <td>${{ number_format($category->revenue, 2) }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        {{ number_format($category->avg_rating, 1) }}
                                        <i class="bi bi-star-fill text-warning ms-1"></i>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Top Products Chart
    const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
    new Chart(topProductsCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($topProducts->pluck('name')) !!},
            datasets: [{
                label: 'Units Sold',
                data: {!! json_encode($topProducts->pluck('units_sold')) !!},
                backgroundColor: 'rgba(75, 192, 192, 0.8)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Category Sales Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($categoryPerformance->pluck('name')) !!},
            datasets: [{
                data: {!! json_encode($categoryPerformance->pluck('revenue')) !!},
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                    'rgba(255, 159, 64, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
});
</script>
@endsection 