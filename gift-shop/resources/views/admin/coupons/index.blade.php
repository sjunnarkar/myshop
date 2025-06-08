@extends('layouts.admin')

@section('title', 'Manage Coupons')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mt-4">Coupons</h1>
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Create New Coupon
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
                            <th>Code</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Usage</th>
                            <th>Status</th>
                            <th>Valid Period</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($coupons as $coupon)
                            <tr>
                                <td>
                                    <strong>{{ $coupon->code }}</strong>
                                    @if($coupon->description)
                                        <br>
                                        <small class="text-muted">{{ $coupon->description }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($coupon->type === 'percentage')
                                        <span class="badge bg-info">Percentage</span>
                                    @else
                                        <span class="badge bg-primary">Fixed Amount</span>
                                    @endif
                                </td>
                                <td>
                                    @if($coupon->type === 'percentage')
                                        {{ $coupon->value }}%
                                    @else
                                        ${{ number_format($coupon->value, 2) }}
                                    @endif
                                    @if($coupon->minimum_spend)
                                        <br>
                                        <small class="text-muted">
                                            Min. spend: ${{ number_format($coupon->minimum_spend, 2) }}
                                        </small>
                                    @endif
                                    @if($coupon->maximum_discount)
                                        <br>
                                        <small class="text-muted">
                                            Max. discount: ${{ number_format($coupon->maximum_discount, 2) }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    @if($coupon->usage_limit)
                                        {{ $coupon->used_count }}/{{ $coupon->usage_limit }}
                                        <div class="progress mt-1" style="height: 4px;">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: {{ ($coupon->used_count / $coupon->usage_limit) * 100 }}%">
                                            </div>
                                        </div>
                                    @else
                                        {{ $coupon->used_count }} uses
                                    @endif
                                </td>
                                <td>
                                    @if($coupon->isValid())
                                        <span class="badge bg-success">Active</span>
                                    @elseif($coupon->isExpired())
                                        <span class="badge bg-danger">Expired</span>
                                    @elseif($coupon->hasReachedLimit())
                                        <span class="badge bg-warning">Limit Reached</span>
                                    @elseif(!$coupon->is_active)
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @if($coupon->starts_at)
                                        From: {{ $coupon->starts_at->format('M d, Y') }}<br>
                                    @endif
                                    @if($coupon->expires_at)
                                        Until: {{ $coupon->expires_at->format('M d, Y') }}
                                    @else
                                        No expiration
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.coupons.edit', $coupon) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.coupons.destroy', $coupon) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this coupon?');"
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
                                    <div class="text-muted">No coupons found</div>
                                    <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary mt-3">
                                        Create First Coupon
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $coupons->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 