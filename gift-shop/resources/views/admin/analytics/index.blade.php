@extends('layouts.admin')

@section('title', 'Analytics Dashboard')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.css">
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Analytics Dashboard</h1>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-calendar"></i> Last 30 Days
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#">Last 7 Days</a></li>
                <li><a class="dropdown-item" href="#">Last 30 Days</a></li>
                <li><a class="dropdown-item" href="#">This Month</a></li>
                <li><a class="dropdown-item" href="#">Last Month</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#">Custom Range</a></li>
            </ul>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($totalRevenue, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-currency-dollar fs-2 text-gray-300"></i>
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
                                Average Order Value
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($averageOrderValue, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-cart fs-2 text-gray-300"></i>
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
                                Total Customers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($totalCustomers) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people fs-2 text-gray-300"></i>
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
                                Total Orders
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($totalOrders) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-bag fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Sales Trend Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-graph-up me-1"></i>
                    Sales Trend
                </div>
                <div class="card-body">
                    <canvas id="salesTrendChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Categories Pie Chart -->
        <div class="col-xl-4 col-lg-5">
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

    <!-- Top Products and Categories -->
    <div class="row">
        <!-- Top Products -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-star me-1"></i>
                    Top Selling Products
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Sales</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topProducts as $product)
                                    <tr>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->sales_count }}</td>
                                        <td>${{ number_format($product->sales_count * $product->base_price, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Categories -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-grid me-1"></i>
                    Top Categories
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Products</th>
                                    <th>Sales</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topCategories as $category)
                                    <tr>
                                        <td>{{ $category->name }}</td>
                                        <td>{{ $category->products_count }}</td>
                                        <td>{{ $category->sales_count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sales Trend Chart
    const salesTrendCtx = document.getElementById('salesTrendChart').getContext('2d');
    new Chart(salesTrendCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($salesTrend->pluck('date')) !!},
            datasets: [{
                label: 'Revenue',
                data: {!! json_encode($salesTrend->pluck('revenue')) !!},
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value;
                        }
                    }
                }
            }
        }
    });

    // Category Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($topCategories->pluck('name')) !!},
            datasets: [{
                data: {!! json_encode($topCategories->pluck('sales_count')) !!},
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
});
</script>
@endsection 