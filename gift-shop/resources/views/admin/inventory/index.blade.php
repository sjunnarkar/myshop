@extends('layouts.admin')

@section('admin_content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Inventory Management</h1>
        <div>
            <a href="{{ route('admin.inventory.export') }}" class="btn btn-success">
                <i class="fas fa-file-export"></i> Export
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#batchUpdateModal">
                <i class="fas fa-edit"></i> Batch Update
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Low Stock Alerts -->
    @if($lowStockItems->isNotEmpty())
        <div class="card mb-4 border-left-warning">
            <div class="card-header bg-warning text-white">
                <i class="fas fa-exclamation-triangle"></i> Low Stock Alerts
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Current Stock</th>
                                <th>Reorder Point</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lowStockItems as $item)
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td>
                                        <span class="badge bg-danger">{{ $item->stock_level }}</span>
                                    </td>
                                    <td>{{ $item->reorder_point }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary" 
                                                onclick="openAdjustModal({{ $item->id }})">
                                            Adjust Stock
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Inventory Table -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-warehouse me-1"></i>
            Current Inventory
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="inventoryTable">
                    <thead>
                        <tr>
                            <th style="cursor: pointer">Product <i class="fas fa-sort"></i></th>
                            <th style="cursor: pointer">SKU <i class="fas fa-sort"></i></th>
                            <th style="cursor: pointer">Stock Level <i class="fas fa-sort"></i></th>
                            <th style="cursor: pointer">Reorder Point <i class="fas fa-sort"></i></th>
                            <th style="cursor: pointer">Last Movement <i class="fas fa-sort"></i></th>
                            <th style="cursor: pointer">Value <i class="fas fa-sort"></i></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventory as $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ $item->product->sku }}</td>
                                <td>
                                    <span class="badge {{ $item->stock_level <= $item->reorder_point ? 'bg-danger' : 'bg-success' }}">
                                        {{ $item->stock_level }}
                                    </span>
                                </td>
                                <td>{{ $item->reorder_point }}</td>
                                <td>
                                    {{ $item->latestMovement ? $item->latestMovement->created_at->diffForHumans() : 'N/A' }}
                                </td>
                                <td>₹{{ number_format($item->stock_level * $item->unit_cost, 2) }}</td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-primary" 
                                                onclick="openAdjustModal({{ $item->id }})">
                                            Adjust
                                        </button>
                                        <a href="{{ route('admin.inventory.show', $item) }}" 
                                           class="btn btn-sm btn-info">
                                            History
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No inventory items found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $inventory->links() }}
        </div>
    </div>
</div>

<!-- Stock Adjustment Modal -->
<div class="modal fade" id="adjustStockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="adjustStockForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Adjust Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Adjustment Type</label>
                        <select name="type" class="form-select" required>
                            <option value="add">Add Stock</option>
                            <option value="subtract">Remove Stock</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="quantity" class="form-control" required min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <input type="text" name="reason" class="form-control" required>
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

<!-- Batch Update Modal -->
<div class="modal fade" id="batchUpdateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.inventory.batch-update') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Batch Update Inventory</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Current Stock</th>
                                    <th>New Stock Level</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($inventory as $item)
                                    <tr>
                                        <td>{{ $item->product->name }}</td>
                                        <td>{{ $item->stock_level }}</td>
                                        <td>
                                            <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}">
                                            <input type="number" name="items[{{ $loop->index }}][quantity]" 
                                                   class="form-control form-control-sm" 
                                                   value="{{ $item->stock_level }}" min="0">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Update Reason</label>
                        <input type="text" name="reason" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update All</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openAdjustModal(itemId) {
        const modal = document.getElementById('adjustStockModal');
        const form = modal.querySelector('#adjustStockForm');
        form.action = `{{ url('admin/inventory') }}/${itemId}/adjust`;
        
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    }

    // Basic table sorting
    document.addEventListener('DOMContentLoaded', function() {
        const getCellValue = (tr, idx) => tr.children[idx].innerText || tr.children[idx].textContent;

        const comparer = (idx, asc) => (a, b) => ((v1, v2) => 
            v1 !== '' && v2 !== '' && !isNaN(v1) && !isNaN(v2) ? v1 - v2 : v1.toString().localeCompare(v2)
        )(getCellValue(asc ? a : b, idx), getCellValue(asc ? b : a, idx));

        document.querySelectorAll('th').forEach(th => th.addEventListener('click', (() => {
            const table = th.closest('table');
            const tbody = table.querySelector('tbody');
            Array.from(tbody.querySelectorAll('tr'))
                .sort(comparer(Array.from(th.parentNode.children).indexOf(th), this.asc = !this.asc))
                .forEach(tr => tbody.appendChild(tr));
        })));
    });
</script>
@endpush 