@extends('layouts.admin')

@section('title', 'Manage Orders')

@section('admin_title')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <i class="bi bi-cart"></i> Orders
        </div>
    </div>
@endsection

@section('admin_content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Orders</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Orders</li>
        </ol>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-funnel"></i> Filter Orders
            </div>
            <div class="card-body">
                <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="order_number" class="form-label">Order Number</label>
                        <input type="text" class="form-control" id="order_number" name="order_number" 
                               value="{{ request('order_number') }}" placeholder="Search order number">
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="all">All Status</option>
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="from_date" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="from_date" name="from_date" 
                               value="{{ request('from_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="to_date" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="to_date" name="to_date" 
                               value="{{ request('to_date') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="d-grid gap-2 d-md-flex w-100">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Filter
                            </button>
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ $order->user->name }}</td>
                                    <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                    <td>{{ $order->items->sum('quantity') }} items</td>
                                    <td>₹{{ number_format($order->total_amount, 2) }}</td>
                                    <td>
                                        <form action="{{ route('admin.orders.status.update', $order) }}" 
                                              method="POST" 
                                              class="d-flex gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <select class="form-select form-select-sm" name="status" 
                                                    onchange="this.form.submit()"
                                                    style="width: 130px;">
                                                @foreach($statuses as $value => $label)
                                                    <option value="{{ $value }}" 
                                                            {{ $order->status === $value ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.orders.show', $order) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.orders.invoice', $order) }}" 
                                               class="btn btn-sm btn-secondary">
                                                <i class="bi bi-file-pdf"></i>
                                            </a>
                                            @if($order->status === 'cancelled')
                                                <form action="{{ route('admin.orders.destroy', $order) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this order?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bi bi-box display-4 text-muted mb-3"></i>
                                            <h5>No Orders Found</h5>
                                            <p class="text-muted">No orders match your search criteria.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} orders
                    </div>
                    <div>
                        {{ $orders->onEachSide(1)->links('vendor.pagination.bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 