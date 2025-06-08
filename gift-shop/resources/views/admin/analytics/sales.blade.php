@extends('layouts.admin')

@section('title', 'Sales Analytics')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.css">
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Sales Analytics</h1>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-calendar"></i> Time Period
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#">Today</a></li>
                <li><a class="dropdown-item" href="#">This Week</a></li>
                <li><a class="dropdown-item" href="#">This Month</a></li>
                <li><a class="dropdown-item" href="#">Last Month</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#">Custom Range</a></li>
            </ul>
        </div>
    </div>

    <!-- Sales Overview Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Today's Sales
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($metrics['today'], 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-check fs-2 text-gray-300"></i>
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
                                This Month
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($metrics['this_month'], 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-month fs-2 text-gray-300"></i>
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
                                Last Month
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($metrics['last_month'], 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-minus fs-2 text-gray-300"></i>
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
                                Average Order
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($metrics['average_order'], 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calculator fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Payment Methods Chart -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-credit-card me-1"></i>
                    Sales by Payment Method
                </div>
                <div class="card-body">
                    <canvas id="paymentMethodChart" width="100%" height="50"></canvas>
                </div>
            </div>
        </div>

        <!-- Hourly Sales Distribution -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-clock me-1"></i>
                    Hourly Sales Distribution
                </div>
                <div class="card-body">
                    <canvas id="hourlyDistributionChart" width="100%" height="50"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Details Table -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-table me-1"></i>
            Sales by Payment Method
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Payment Method</th>
                            <th>Number of Orders</th>
                            <th>Total Sales</th>
                            <th>Average Order Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salesByPayment as $payment)
                            <tr>
                                <td>{{ ucfirst($payment->payment_method) }}</td>
                                <td>{{ number_format($payment->count) }}</td>
                                <td>${{ number_format($payment->total, 2) }}</td>
                                <td>${{ number_format($payment->total / $payment->count, 2) }}</td>
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
    // Payment Methods Chart
    const paymentCtx = document.getElementById('paymentMethodChart').getContext('2d');
    new Chart(paymentCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($salesByPayment->pluck('payment_method')) !!},
            datasets: [{
                data: {!! json_encode($salesByPayment->pluck('total')) !!},
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Hourly Distribution Chart
    const hourlyCtx = document.getElementById('hourlyDistributionChart').getContext('2d');
    new Chart(hourlyCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($hourlyDistribution->pluck('hour')) !!},
            datasets: [{
                label: 'Number of Orders',
                data: {!! json_encode($hourlyDistribution->pluck('count')) !!},
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
                },
                x: {
                    title: {
                        display: true,
                        text: 'Hour of Day (24h)'
                    }
                }
            }
        }
    });
});
</script>
@endsection 