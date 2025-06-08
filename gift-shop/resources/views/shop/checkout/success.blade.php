@extends('layouts.shop')

@section('title', 'Order Confirmation')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="text-center mb-5">
                <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                <h1 class="h3 mt-3">Thank You for Your Order!</h1>
                <p class="text-muted">Order #{{ $order->order_number }}</p>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Order Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Shipping Address</h6>
                            <p class="mb-1">{{ $order->shipping_name }}</p>
                            <p class="mb-1">{{ $order->shipping_address }}</p>
                            <p class="mb-1">{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zip }}</p>
                            <p class="mb-1">{{ $order->shipping_country }}</p>
                            <p class="mb-0">{{ $order->shipping_phone }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Billing Address</h6>
                            <p class="mb-1">{{ $order->billing_name }}</p>
                            <p class="mb-1">{{ $order->billing_address }}</p>
                            <p class="mb-1">{{ $order->billing_city }}, {{ $order->billing_state }} {{ $order->billing_zip }}</p>
                            <p class="mb-1">{{ $order->billing_country }}</p>
                            <p class="mb-0">{{ $order->billing_phone }}</p>
                        </div>
                    </div>

                    <h6 class="text-muted mb-3">Order Items</h6>
                    @foreach($order->items as $item)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-0">{{ $item->product->name }}</h6>
                                <small class="text-muted">Qty: {{ $item->quantity }}</small>
                                
                                @if($item->customization)
                                    <div class="small text-muted mt-1">
                                        <strong>Customization:</strong>
                                        @foreach($item->customization as $area => $details)
                                            <div>{{ $area }}: {{ $details['text'] }} ({{ $details['font'] }})</div>
                                        @endforeach
                                    </div>
                                @endif

                                @if($item->options)
                                    <div class="small text-muted mt-1">
                                        <strong>Options:</strong>
                                        @foreach($item->options as $option => $value)
                                            <div>{{ $option }}: {{ $value }}</div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <span>${{ number_format($item->price * $item->quantity, 2) }}</span>
                        </div>
                    @endforeach

                    <hr>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span>${{ number_format($order->total_amount, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping</span>
                        <span class="text-success">Free</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <strong>Total</strong>
                        <strong>${{ number_format($order->total_amount, 2) }}</strong>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <p class="mb-4">
                    We've sent a confirmation email to {{ $order->billing_email }} with your order details.
                </p>
                <a href="{{ route('shop.index') }}" class="btn btn-primary">
                    Continue Shopping
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 