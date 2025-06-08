@extends('layouts.admin')

@section('title', 'Order Details')

@section('admin_title')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <i class="bi bi-box"></i> Order Details
        </div>
    </div>
@endsection

@section('admin_content')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
                <li class="breadcrumb-item active">Order #{{ $order->order_number }}</li>
            </ol>
        </nav>
        <a href="{{ route('admin.orders.invoice', $order) }}" class="btn btn-secondary">
            <i class="bi bi-file-pdf"></i> Download Invoice
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center mt-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center mt-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Order Header -->
    <div class="card mt-3">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">Order #{{ $order->order_number }}</h5>
                    <p class="mb-0 text-muted">
                        Placed on {{ $order->created_at->format('F d, Y H:i:s') }}
                    </p>
                </div>
                @if($order->status === 'cancelled')
                    <form action="{{ route('admin.orders.destroy', $order) }}" 
                          method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this order?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Delete Order
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Order Details -->
        <div class="col-lg-8">
            <!-- Status Update -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Order Status</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.status.update', $order) }}" 
                          method="POST" 
                          class="row align-items-end">
                        @csrf
                        @method('PATCH')
                        <div class="col-md-6">
                            <label for="status" class="form-label">Current Status</label>
                            <select class="form-select" id="status" name="status">
                                @foreach(['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $status)
                                    <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary">
                                Update Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Order Items</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item->product)
                                                    <img src="{{ Storage::url($item->product->thumbnail) }}" 
                                                         alt="{{ $item->product->name }}"
                                                         class="rounded"
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                    <div class="ms-3">
                                                        <h6 class="mb-1">{{ $item->product->name }}</h6>
                                                @else
                                                    <div class="ms-3">
                                                        <h6 class="mb-1 text-muted">[Deleted Product]</h6>
                                                @endif
                                                @if($item->options)
                                                    <small class="text-muted">
                                                        @foreach($item->options as $key => $value)
                                                            {{ ucfirst($key) }}: {{ $value }}
                                                            @if(!$loop->last), @endif
                                                        @endforeach
                                                    </small>
                                                @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>₹{{ number_format($item->unit_price, 2) }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td class="text-end">
                                            ₹{{ number_format($item->subtotal, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-group-divider">
                                <tr>
                                    <td colspan="3" class="text-end">Subtotal:</td>
                                    <td class="text-end">
                                        ₹{{ number_format($order->items->sum('subtotal'), 2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end">Tax (10%):</td>
                                    <td class="text-end">
                                        ₹{{ number_format($order->items->sum('subtotal') * 0.1, 2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end">Shipping:</td>
                                    <td class="text-end">
                                        {{ $order->shipping_cost > 0 ? '₹' . number_format($order->shipping_cost, 2) : 'Free' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Total:</td>
                                    <td class="text-end fw-bold">
                                        ₹{{ number_format($order->total_amount, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="col-lg-4">
            <!-- Customer Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Customer Information</h6>
                </div>
                <div class="card-body">
                    @if($order->user)
                        <p class="mb-1">
                            <strong>Name:</strong> {{ $order->user->name }}
                        </p>
                        <p class="mb-1">
                            <strong>Email:</strong> {{ $order->user->email }}
                        </p>
                        <p class="mb-1">
                            <strong>Phone:</strong> {{ $order->user->phone ?? 'Not provided' }}
                        </p>
                        <p class="mb-0">
                            <strong>Member since:</strong> {{ $order->user->created_at->format('M d, Y') }}
                        </p>
                    @else
                        <p class="text-muted mb-0">Customer account has been deleted</p>
                    @endif
                </div>
            </div>

            <!-- Payment Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Payment Information</h6>
                </div>
                <div class="card-body">
                    <p class="mb-1">
                        <span class="text-muted">Method:</span>
                        {{ ucfirst($order->payment_method) }}
                    </p>
                    <p class="mb-0">
                        <span class="text-muted">Status:</span>
                        @if($order->payment_status === 'paid')
                            <span class="badge bg-success">Paid</span>
                        @else
                            <span class="badge bg-warning text-dark">{{ ucfirst($order->payment_status) }}</span>
                        @endif
                    </p>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Shipping Address</h6>
                </div>
                <div class="card-body">
                    <div class="col-md-6">
                        <h6 class="mb-3">Shipping Address</h6>
                        <address>
                            @if($order->shippingAddress)
                                {{ $order->shippingAddress->name }}<br>
                                {{ $order->shippingAddress->street_address }}<br>
                                {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->postal_code }}<br>
                                {{ $order->shippingAddress->country }}<br>
                                Phone: {{ $order->shippingAddress->phone }}
                            @else
                                No shipping address found
                            @endif
                        </address>
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-3">Billing Address</h6>
                        <address>
                            @if($order->billingAddress)
                                {{ $order->billingAddress->name }}<br>
                                {{ $order->billingAddress->street_address }}<br>
                                {{ $order->billingAddress->city }}, {{ $order->billingAddress->state }} {{ $order->billingAddress->postal_code }}<br>
                                {{ $order->billingAddress->country }}<br>
                                Phone: {{ $order->billingAddress->phone }}
                            @else
                                No billing address found
                            @endif
                        </address>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 