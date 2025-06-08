@extends('layouts.shop')

@section('title', 'Order Details')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12 mb-4">
            <h4 class="mb-3">Order #{{ $order->order_number }}</h4>
            <p class="text-muted">Placed on {{ $order->created_at->format('M d, Y H:i') }}</p>
            
            <!-- Order Status -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Order Status</h6>
                        <span class="badge bg-{{ $order->status_color }}">{{ ucfirst($order->status) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Order Items</h6>
                </div>
                <div class="card-body">
                    @foreach($order->items as $item)
                        <div class="d-flex mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                            <div class="flex-shrink-0">
                                @if($item->product)
                                    <img src="{{ Storage::url($item->product->thumbnail) }}" 
                                         alt="{{ $item->product->name }}"
                                         class="rounded"
                                         style="width: 64px; height: 64px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded" style="width: 64px; height: 64px;"></div>
                                @endif
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">
                                    @if($item->product)
                                        {{ $item->product->name }}
                                    @else
                                        [Deleted Product]
                                    @endif
                                </h6>
                                <p class="mb-1 text-muted">
                                    Quantity: {{ $item->quantity }} × ₹{{ number_format($item->unit_price, 2) }}
                                </p>
                                @if($item->customization_details)
                                    <small class="text-muted">
                                        <strong>Customization:</strong>
                                        @foreach($item->customization_details as $key => $value)
                                            {{ $key }}: {{ $value }}
                                            @if(!$loop->last), @endif
                                        @endforeach
                                    </small>
                                @endif
                            </div>
                            <div class="flex-shrink-0 ms-3">
                                <span class="fw-bold">₹{{ number_format($item->subtotal, 2) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="card-footer bg-white">
                    <div class="row justify-content-end">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td>Subtotal:</td>
                                    <td class="text-end">₹{{ number_format($order->items->sum('subtotal'), 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Shipping:</td>
                                    <td class="text-end">
                                        {{ $order->shipping_cost > 0 ? '₹' . number_format($order->shipping_cost, 2) : 'Free' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tax (10%):</td>
                                    <td class="text-end">₹{{ number_format($order->items->sum('subtotal') * 0.1, 2) }}</td>
                                </tr>
                                <tr class="border-top">
                                    <td><strong>Total:</strong></td>
                                    <td class="text-end"><strong>₹{{ number_format($order->total_amount, 2) }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Information -->
        <div class="col-md-4">
            <!-- Payment Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Payment Information</h6>
                </div>
                <div class="card-body">
                    <p class="mb-1">
                        <strong>Method:</strong> {{ ucfirst($order->payment_method) }}
                    </p>
                    <p class="mb-0">
                        <strong>Status:</strong>
                        <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </p>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Shipping Address</h6>
                </div>
                <div class="card-body">
                    @if($order->shippingAddress)
                        <address class="mb-0">
                            {{ $order->shippingAddress->name }}<br>
                            {{ $order->shippingAddress->street_address }}<br>
                            {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->postal_code }}<br>
                            {{ $order->shippingAddress->country }}<br>
                            Phone: {{ $order->shippingAddress->phone }}
                        </address>
                    @else
                        <p class="mb-0 text-muted">No shipping address found</p>
                    @endif
                </div>
            </div>

            <!-- Billing Address -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Billing Address</h6>
                </div>
                <div class="card-body">
                    @if($order->billingAddress)
                        <address class="mb-0">
                            {{ $order->billingAddress->name }}<br>
                            {{ $order->billingAddress->street_address }}<br>
                            {{ $order->billingAddress->city }}, {{ $order->billingAddress->state }} {{ $order->billingAddress->postal_code }}<br>
                            {{ $order->billingAddress->country }}<br>
                            Phone: {{ $order->billingAddress->phone }}
                        </address>
                    @else
                        <p class="mb-0 text-muted">No billing address found</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 