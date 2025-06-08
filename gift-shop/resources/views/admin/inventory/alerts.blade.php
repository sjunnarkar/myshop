@extends('layouts.admin')

@section('admin_content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Low Stock Alerts</h1>
        <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Inventory
        </a>
    </div>

    @if($alerts->isEmpty())
        <div class="alert alert-success" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            All products are well stocked! No low stock alerts at this time.
        </div>
    @else
        <!-- Alert Summary -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card border-left-warning h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Items Needing Attention
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $alerts->count() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Items -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-exclamation-circle me-1"></i>
                Items Below Reorder Point
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Current Stock</th>
                                <th>Reorder Point</th>
                                <th>Units Needed</th>
                                <th>Last Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($alerts as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->product->name }}</strong>
                                        @if($item->stock_level === 0)
                                            <span class="badge bg-danger ms-2">Out of Stock</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->product->sku }}</td>
                                    <td>
                                        <span class="badge bg-danger">{{ $item->stock_level }}</span>
                                    </td>
                                    <td>{{ $item->reorder_point }}</td>
                                    <td>
                                        {{ $item->reorder_point - $item->stock_level }}
                                    </td>
                                    <td>
                                        {{ $item->updated_at->diffForHumans() }}
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-primary" 
                                                    onclick="openAdjustModal({{ $item->id }})">
                                                Add Stock
                                            </button>
                                            <a href="{{ route('admin.inventory.show', $item) }}" 
                                               class="btn btn-sm btn-info">
                                                History
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Restock Recommendations -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-clipboard-list me-1"></i>
                Restock Recommendations
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Recommended Order</th>
                                <th>Estimated Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($alerts as $item)
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td>
                                        {{ $item->reorder_point - $item->stock_level }} units
                                    </td>
                                    <td>
                                        ${{ number_format(($item->reorder_point - $item->stock_level) * $item->unit_cost, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2">Total Estimated Cost:</th>
                                <th>
                                    ${{ number_format($alerts->sum(function($item) {
                                        return ($item->reorder_point - $item->stock_level) * $item->unit_cost;
                                    }), 2) }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Stock Adjustment Modal -->
<div class="modal fade" id="adjustStockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="adjustStockForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="type" value="add">
                    <div class="mb-3">
                        <label class="form-label">Quantity to Add</label>
                        <input type="number" name="quantity" class="form-control" required min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <input type="text" name="reason" class="form-control" required 
                               value="Restocking low inventory">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openAdjustModal(itemId) {
    const modal = document.getElementById('adjustStockModal');
    const form = modal.querySelector('#adjustStockForm');
    form.action = `{{ url('admin/inventory') }}/${itemId}/adjust`;
    
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
}
</script>
@endpush
@endsection 