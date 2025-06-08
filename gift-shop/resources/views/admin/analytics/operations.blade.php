@extends('layouts.admin')

@section('title', 'Operations Analytics')

@section('styles')
<style>
    .metric-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
    .metric-value {
        font-size: 2rem;
        font-weight: bold;
        color: #2d3748;
    }
    .metric-label {
        color: #718096;
        font-size: 0.875rem;
    }
    .progress {
        height: 8px;
        border-radius: 4px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Operations Analytics</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Operations Analytics</li>
    </ol>

    <!-- Key Metrics -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="metric-card">
                <div class="metric-label">Average Processing Time</div>
                <div class="metric-value">{{ number_format($metrics['processing_time'], 1) }} hrs</div>
                <div class="progress mt-2">
                    <div class="progress-bar bg-primary" role="progressbar" 
                         style="width: {{ min(100, ($metrics['processing_time'] / 48) * 100) }}%" 
                         aria-valuenow="{{ $metrics['processing_time'] }}" aria-valuemin="0" aria-valuemax="48">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="metric-card">
                <div class="metric-label">Fulfillment Rate</div>
                <div class="metric-value">{{ number_format($metrics['fulfillment_rate'], 1) }}%</div>
                <div class="progress mt-2">
                    <div class="progress-bar bg-success" role="progressbar" 
                         style="width: {{ $metrics['fulfillment_rate'] }}%" 
                         aria-valuenow="{{ $metrics['fulfillment_rate'] }}" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="metric-card">
                <div class="metric-label">Cancellation Rate</div>
                <div class="metric-value">{{ number_format($metrics['cancellation_rate'], 1) }}%</div>
                <div class="progress mt-2">
                    <div class="progress-bar bg-warning" role="progressbar" 
                         style="width: {{ $metrics['cancellation_rate'] }}%" 
                         aria-valuenow="{{ $metrics['cancellation_rate'] }}" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="metric-card">
                <div class="metric-label">Return Rate</div>
                <div class="metric-value">{{ number_format($metrics['return_rate'], 1) }}%</div>
                <div class="progress mt-2">
                    <div class="progress-bar bg-danger" role="progressbar" 
                         style="width: {{ $metrics['return_rate'] }}%" 
                         aria-valuenow="{{ $metrics['return_rate'] }}" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Processing Times Table -->
    <div class="card mb-4 mt-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Order Processing Times
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Created At</th>
                            <th>Last Updated</th>
                            <th>Processing Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($processingTimes as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ $order->created_at->format('Y-m-d H:i:s') }}</td>
                            <td>{{ $order->updated_at->format('Y-m-d H:i:s') }}</td>
                            <td>
                                @if($order->processing_hours < 24)
                                    {{ $order->processing_hours }} hours
                                @else
                                    {{ number_format($order->processing_hours / 24, 1) }} days
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : 
                                    ($order->status === 'processing' ? 'primary' : 
                                    ($order->status === 'cancelled' ? 'danger' : 'warning')) }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $processingTimes->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Add any additional JavaScript for interactivity here
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize any necessary JavaScript functionality
    });
</script>
@endsection 