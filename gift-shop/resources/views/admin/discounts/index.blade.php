@extends('layouts.admin')

@section('title', 'Manage Discounts')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mt-4">Discounts</h1>
        <a href="{{ route('admin.discounts.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Create New Discount
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mt-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="card mt-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Period</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($discounts as $discount)
                            <tr>
                                <td>
                                    <strong>{{ $discount->name }}</strong>
                                    @if($discount->description)
                                        <br>
                                        <small class="text-muted">{{ $discount->description }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($discount->type === 'percentage')
                                        <span class="badge bg-info">Percentage</span>
                                    @elseif($discount->type === 'fixed')
                                        <span class="badge bg-primary">Fixed Amount</span>
                                    @else
                                        <span class="badge bg-warning">Buy {{ $discount->buy_x }} Get {{ $discount->get_y }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($discount->type === 'percentage')
                                        {{ $discount->value }}%
                                    @elseif($discount->type === 'fixed')
                                        ${{ number_format($discount->value, 2) }}
                                    @else
                                        Buy {{ $discount->buy_x }} Get {{ $discount->get_y }} Free
                                    @endif
                                    @if($discount->minimum_spend)
                                        <br>
                                        <small class="text-muted">
                                            Min. spend: ${{ number_format($discount->minimum_spend, 2) }}
                                        </small>
                                    @endif
                                    @if($discount->maximum_discount)
                                        <br>
                                        <small class="text-muted">
                                            Max. discount: ${{ number_format($discount->maximum_discount, 2) }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    @if($discount->starts_at)
                                        From: {{ $discount->starts_at->format('M d, Y') }}<br>
                                    @endif
                                    @if($discount->expires_at)
                                        Until: {{ $discount->expires_at->format('M d, Y') }}
                                    @else
                                        No expiration
                                    @endif
                                </td>
                                <td>
                                    @if($discount->isValid())
                                        <span class="badge bg-success">Active</span>
                                    @elseif($discount->isExpired())
                                        <span class="badge bg-danger">Expired</span>
                                    @elseif(!$discount->is_active)
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                    @if($discount->stackable)
                                        <br>
                                        <small class="text-success">Stackable</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $discount->priority }}</span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.discounts.edit', $discount) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.discounts.destroy', $discount) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this discount?');"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">No discounts found</div>
                                    <a href="{{ route('admin.discounts.create') }}" class="btn btn-primary mt-3">
                                        Create First Discount
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $discounts->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 