@extends('layouts.admin')

@section('title', 'Marketing Analytics')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.css">
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Marketing Analytics</h1>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-calendar"></i> <span id="selectedPeriod">Last 30 Days</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#" data-period="7days">Last 7 Days</a></li>
                <li><a class="dropdown-item" href="#" data-period="30days">Last 30 Days</a></li>
                <li><a class="dropdown-item" href="#" data-period="90days">Last 90 Days</a></li>
                <li><a class="dropdown-item" href="#" data-period="year">Last Year</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" data-period="custom">Custom Range</a></li>
            </ul>
        </div>
    </div>

    <!-- Marketing Overview Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Active Campaigns
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($metrics['active_campaigns']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-megaphone fs-2 text-gray-300"></i>
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
                                Newsletter Subscribers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($metrics['subscribers']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-envelope fs-2 text-gray-300"></i>
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
                                Active Coupons
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($metrics['active_coupons']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-ticket-perforated fs-2 text-gray-300"></i>
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
                                Conversion Rate
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($metrics['conversion_rate'], 1) }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-graph-up-arrow fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Newsletter Metrics Chart -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-graph-up me-1"></i>
                    Newsletter Performance
                </div>
                <div class="card-body">
                    <canvas id="newsletterChart" width="100%" height="50"></canvas>
                </div>
            </div>
        </div>

        <!-- Coupon Usage Chart -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-pie-chart me-1"></i>
                    Coupon Usage Distribution
                </div>
                <div class="card-body">
                    <canvas id="couponChart" width="100%" height="50"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Campaign Performance Table -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-table me-1"></i>
            Campaign Performance
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Campaign</th>
                            <th>Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Reach</th>
                            <th>Conversions</th>
                            <th>Revenue</th>
                            <th>ROI</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($campaigns as $campaign)
                            <tr>
                                <td>{{ $campaign->name }}</td>
                                <td>{{ ucfirst($campaign->type) }}</td>
                                <td>{{ $campaign->start_date->format('M d, Y') }}</td>
                                <td>{{ $campaign->end_date->format('M d, Y') }}</td>
                                <td>{{ number_format($campaign->reach) }}</td>
                                <td>{{ number_format($campaign->conversions) }}</td>
                                <td>${{ number_format($campaign->revenue, 2) }}</td>
                                <td>{{ number_format($campaign->roi, 1) }}%</td>
                                <td>
                                    @if($campaign->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($campaign->status === 'scheduled')
                                        <span class="badge bg-info">Scheduled</span>
                                    @elseif($campaign->status === 'completed')
                                        <span class="badge bg-secondary">Completed</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Coupon Performance -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-ticket me-1"></i>
            Coupon Performance
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Coupon Code</th>
                            <th>Discount Type</th>
                            <th>Value</th>
                            <th>Usage Count</th>
                            <th>Total Savings</th>
                            <th>Avg Order Value</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coupons as $coupon)
                            <tr>
                                <td>{{ $coupon->code }}</td>
                                <td>{{ ucfirst($coupon->discount_type) }}</td>
                                <td>
                                    @if($coupon->discount_type === 'percentage')
                                        {{ number_format($coupon->value) }}%
                                    @else
                                        ${{ number_format($coupon->value, 2) }}
                                    @endif
                                </td>
                                <td>{{ number_format($coupon->usage_count) }}</td>
                                <td>${{ number_format($coupon->total_savings, 2) }}</td>
                                <td>${{ number_format($coupon->avg_order_value, 2) }}</td>
                                <td>{{ $coupon->expiry_date->format('M d, Y') }}</td>
                                <td>
                                    @if($coupon->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
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
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let newsletterChart, couponChart;
    const selectedPeriodEl = document.getElementById('selectedPeriod');

    // Initialize charts
    function initCharts(data) {
        // Newsletter Performance Chart
        const newsletterCtx = document.getElementById('newsletterChart').getContext('2d');
        newsletterChart = new Chart(newsletterCtx, {
            type: 'line',
            data: {
                labels: data.newsletterMetrics.map(item => item.date),
                datasets: [{
                    label: 'Subscribers',
                    data: data.newsletterMetrics.map(item => item.subscribers),
                    borderColor: 'rgba(75, 192, 192, 1)',
                    tension: 0.1,
                    fill: false
                }, {
                    label: 'Open Rate',
                    data: data.newsletterMetrics.map(item => item.open_rate),
                    borderColor: 'rgba(255, 99, 132, 1)',
                    tension: 0.1,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Coupon Usage Chart
        const couponCtx = document.getElementById('couponChart').getContext('2d');
        couponChart = new Chart(couponCtx, {
            type: 'doughnut',
            data: {
                labels: data.couponUsage.map(item => item.name),
                datasets: [{
                    data: data.couponUsage.map(item => item.usage_count),
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
    }

    // Update charts and metrics
    function updateData(period, startDate = null, endDate = null) {
        const params = new URLSearchParams();
        params.append('period', period);
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);

        fetch(`/admin/analytics/marketing?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                // Update metrics
                Object.keys(data.metrics).forEach(key => {
                    const el = document.querySelector(`[data-metric="${key}"]`);
                    if (el) {
                        if (key === 'conversion_rate') {
                            el.textContent = `${data.metrics[key].toFixed(1)}%`;
                        } else {
                            el.textContent = new Intl.NumberFormat().format(data.metrics[key]);
                        }
                    }
                });

                // Update charts
                if (newsletterChart) newsletterChart.destroy();
                if (couponChart) couponChart.destroy();
                initCharts(data);

                // Update tables
                updateTables(data);
            })
            .catch(error => console.error('Error:', error));
    }

    // Update tables with new data
    function updateTables(data) {
        // Update campaign performance table
        const campaignTableBody = document.querySelector('#campaignTable tbody');
        if (campaignTableBody) {
            campaignTableBody.innerHTML = data.campaigns.map(campaign => `
                <tr>
                    <td>${campaign.name}</td>
                    <td>${campaign.type}</td>
                    <td>${new Date(campaign.start_date).toLocaleDateString()}</td>
                    <td>${new Date(campaign.end_date).toLocaleDateString()}</td>
                    <td>${new Intl.NumberFormat().format(campaign.reach)}</td>
                    <td>${new Intl.NumberFormat().format(campaign.conversions_count)}</td>
                    <td>$${new Intl.NumberFormat().format(campaign.revenue)}</td>
                    <td>${campaign.roi.toFixed(1)}%</td>
                    <td>
                        <span class="badge bg-${campaign.status === 'active' ? 'success' : 
                                            campaign.status === 'scheduled' ? 'info' : 
                                            campaign.status === 'completed' ? 'secondary' : 
                                            'danger'}">
                            ${campaign.status.charAt(0).toUpperCase() + campaign.status.slice(1)}
                        </span>
                    </td>
                </tr>
            `).join('');
        }

        // Update coupon performance table
        const couponTableBody = document.querySelector('#couponTable tbody');
        if (couponTableBody) {
            couponTableBody.innerHTML = data.coupons.map(coupon => `
                <tr>
                    <td>${coupon.code}</td>
                    <td>${coupon.discount_type.charAt(0).toUpperCase() + coupon.discount_type.slice(1)}</td>
                    <td>${coupon.discount_type === 'percentage' ? 
                        `${coupon.value}%` : 
                        `$${new Intl.NumberFormat().format(coupon.value)}`}</td>
                    <td>${new Intl.NumberFormat().format(coupon.usage_count)}</td>
                    <td>$${new Intl.NumberFormat().format(coupon.total_savings)}</td>
                    <td>$${new Intl.NumberFormat().format(coupon.avg_order_value)}</td>
                    <td>${new Date(coupon.expires_at).toLocaleDateString()}</td>
                    <td>
                        <span class="badge bg-${coupon.is_active ? 'success' : 'danger'}">
                            ${coupon.is_active ? 'Active' : 'Inactive'}
                        </span>
                    </td>
                </tr>
            `).join('');
        }
    }

    // Handle time period selection
    document.querySelectorAll('.dropdown-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const period = this.dataset.period;
            selectedPeriodEl.textContent = this.textContent;

            if (period === 'custom') {
                // Show date range picker modal
                // Implementation depends on your date picker library
                // For example, using Bootstrap's modal and DateRangePicker
                $('#dateRangeModal').modal('show');
            } else {
                updateData(period);
            }
        });
    });

    // Initial load
    updateData('30days');
});
</script>
@endsection 