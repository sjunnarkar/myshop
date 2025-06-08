@extends('layouts.admin')

@section('title', 'Customer Analytics')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.css">
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Customer Analytics</h1>
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

    <!-- Customer Overview Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Customers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($metrics['total_customers']) }}
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
            <div class="card border-left-success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                New This Month
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($metrics['new_customers']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-plus fs-2 text-gray-300"></i>
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
                                Average Order Value
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($metrics['avg_order_value'], 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-cart-check fs-2 text-gray-300"></i>
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
                                Retention Rate
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($metrics['retention_rate'], 1) }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-arrow-repeat fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Customer Acquisition Chart -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-graph-up me-1"></i>
                    Customer Acquisition Trend
                </div>
                <div class="card-body">
                    <canvas id="acquisitionChart" width="100%" height="50"></canvas>
                </div>
            </div>
        </div>

        <!-- Customer Segments Chart -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-pie-chart me-1"></i>
                    Customer Segments
                </div>
                <div class="card-body">
                    <canvas id="segmentsChart" width="100%" height="50"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Customers Table -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-trophy me-1"></i>
            Top Customers
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Total Orders</th>
                            <th>Total Spent</th>
                            <th>Average Order Value</th>
                            <th>Last Order</th>
                            <th>Customer Since</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topCustomers as $customer)
                            <tr>
                                <td>{{ $customer->name }}</td>
                                <td>{{ number_format($customer->total_orders) }}</td>
                                <td>${{ number_format($customer->total_spent, 2) }}</td>
                                <td>${{ number_format($customer->avg_order_value, 2) }}</td>
                                <td>{{ $customer->last_order_date->format('M d, Y') }}</td>
                                <td>{{ $customer->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Customer Geographic Distribution -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-geo-alt me-1"></i>
            Geographic Distribution
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Region</th>
                            <th>Number of Customers</th>
                            <th>Total Orders</th>
                            <th>Total Revenue</th>
                            <th>Average Order Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($geographicData as $region)
                            <tr>
                                <td>{{ $region->name }}</td>
                                <td>{{ number_format($region->customer_count) }}</td>
                                <td>{{ number_format($region->order_count) }}</td>
                                <td>${{ number_format($region->total_revenue, 2) }}</td>
                                <td>${{ number_format($region->avg_order_value, 2) }}</td>
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
    // Customer Acquisition Chart
    const acquisitionCtx = document.getElementById('acquisitionChart').getContext('2d');
    new Chart(acquisitionCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($acquisitionTrend->pluck('date')) !!},
            datasets: [{
                label: 'New Customers',
                data: {!! json_encode($acquisitionTrend->pluck('count')) !!},
                borderColor: 'rgba(75, 192, 192, 1)',
                tension: 0.1,
                fill: false
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

    // Customer Segments Chart
    const segmentsCtx = document.getElementById('segmentsChart').getContext('2d');
    new Chart(segmentsCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($segments->pluck('name')) !!},
            datasets: [{
                data: {!! json_encode($segments->pluck('count')) !!},
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