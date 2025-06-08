@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                Inventory History: {{ $inventory->product->name }}
            </h1>
            <p class="text-muted">
                SKU: {{ $inventory->product->sku }}
            </p>
        </div>
        <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Inventory
        </a>
    </div>

    <!-- Current Stock Info Card -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-primary h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Current Stock Level
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $inventory->stock_level }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left-success h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Value
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ₹{{ number_format($inventory->stock_level * $inventory->unit_cost, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left-info h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Reorder Point
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $inventory->reorder_point }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-flag fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left-warning h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Unit Cost
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ₹{{ number_format($inventory->unit_cost, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Movement History -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-history me-1"></i>
            Stock Movement History
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Reason</th>
                            <th>Updated By</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventory->movements as $movement)
                            <tr>
                                <td>{{ $movement->created_at->format('M d, Y H:i:s') }}</td>
                                <td>
                                    @if($movement->type === 'add')
                                        <span class="badge bg-success">Stock Added</span>
                                    @elseif($movement->type === 'subtract')
                                        <span class="badge bg-danger">Stock Removed</span>
                                    @elseif($movement->type === 'order')
                                        <span class="badge bg-info">Order Placed</span>
                                    @elseif($movement->type === 'return')
                                        <span class="badge bg-warning">Return</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($movement->type) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($movement->quantity > 0)
                                        <span class="text-success">+{{ $movement->quantity }}</span>
                                    @else
                                        <span class="text-danger">{{ $movement->quantity }}</span>
                                    @endif
                                </td>
                                <td>{{ $movement->reason }}</td>
                                <td>{{ $movement->user->name }}</td>
                                <td>
                                    @if($movement->notes)
                                        <button type="button" class="btn btn-sm btn-link" 
                                                data-bs-toggle="tooltip" 
                                                title="{{ $movement->notes }}">
                                            View Notes
                                        </button>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $inventory->movements->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
@endsection 