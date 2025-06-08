<!-- Order Header -->
<div class="card mb-2">
    <div class="card-header bg-white py-2">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">Order #{{ $order->order_number }}</h5>
                <p class="mb-0 text-muted small">
                    Placed on {{ $order->created_at->format('F d, Y') }}
                </p>
            </div>
            <div class="text-end">
                <span class="badge bg-{{ $order->status_color }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Order Progress -->
<div class="card mb-2">
    <div class="card-body p-2">
        <div class="d-flex justify-content-between position-relative">
            <div class="d-flex flex-column align-items-center" style="z-index: 1;">
                <div class="rounded-circle {{ $order->status == 'pending' ? 'bg-warning' : ($order->status == 'processing' ? 'bg-info' : ($order->status == 'shipped' ? 'bg-primary' : ($order->status == 'delivered' ? 'bg-success' : 'bg-secondary'))) }} text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="bi bi-cart-check"></i>
                </div>
                <span class="small mt-1">Ordered</span>
            </div>
            <div class="d-flex flex-column align-items-center" style="z-index: 1;">
                <div class="rounded-circle {{ $order->status == 'processing' ? 'bg-info' : ($order->status == 'shipped' || $order->status == 'delivered' ? 'bg-success' : 'bg-secondary') }} text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="bi bi-box-seam"></i>
                </div>
                <span class="small mt-1">Processing</span>
            </div>
            <div class="d-flex flex-column align-items-center" style="z-index: 1;">
                <div class="rounded-circle {{ $order->status == 'shipped' ? 'bg-primary' : ($order->status == 'delivered' ? 'bg-success' : 'bg-secondary') }} text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="bi bi-truck"></i>
                </div>
                <span class="small mt-1">Shipped</span>
            </div>
            <div class="d-flex flex-column align-items-center" style="z-index: 1;">
                <div class="rounded-circle {{ $order->status == 'delivered' ? 'bg-success' : 'bg-secondary' }} text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="bi bi-check-lg"></i>
                </div>
                <span class="small mt-1">Delivered</span>
            </div>
            <!-- Progress Line -->
            <div class="position-absolute top-50 start-0 end-0 translate-middle-y">
                <div class="progress" style="height: 2px;">
                    <div class="progress-bar {{ $order->status == 'pending' ? 'bg-warning' : ($order->status == 'processing' ? 'bg-info' : ($order->status == 'shipped' ? 'bg-primary' : ($order->status == 'delivered' ? 'bg-success' : 'bg-secondary'))) }}" role="progressbar" style="width: {{ $order->status == 'pending' ? '25%' : ($order->status == 'processing' ? '50%' : ($order->status == 'shipped' ? '75%' : ($order->status == 'delivered' ? '100%' : '0%'))) }}"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-2">
    <!-- Order Items Column -->
    <div class="col-12">
        <!-- Order Items -->
        <div class="card mb-2">
            <div class="card-header bg-white py-2">
                <h6 class="mb-0">Order Items</h6>
            </div>
            <div class="card-body p-2">
                @foreach($order->items as $item)
                    <div class="d-flex mb-2">
                        <div class="flex-shrink-0" style="width: 60px;">
                            <img src="{{ Storage::url($item->product->thumbnail) }}" 
                                alt="{{ $item->product->name }}"
                                class="img-fluid rounded"
                                style="width: 60px; height: 60px; object-fit: cover;">
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <h6 class="mb-1 small">{{ $item->product->name }}</h6>
                            <p class="mb-1 text-muted small">
                                Quantity: {{ $item->quantity }}
                            </p>
                            @if($item->customization_details)
                                <p class="mb-1 text-muted small">
                                    Options: 
                                    @foreach($item->customization_details as $key => $value)
                                        <span class="me-2">{{ ucfirst($key) }}: {{ $value }}</span>
                                    @endforeach
                                </p>
                            @endif
                            <p class="mb-0 small">
                                <span class="text-muted">Price:</span>
                                {{ config('app.currency_symbol') }}{{ number_format($item->unit_price, 2) }}
                            </p>
                        </div>
                        <div class="flex-shrink-0 text-end" style="min-width: 80px;">
                            <p class="mb-0 small fw-bold">
                                {{ config('app.currency_symbol') }}{{ number_format($item->subtotal, 2) }}
                            </p>
                        </div>
                    </div>
                    @if(!$loop->last)
                        <hr class="my-2">
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <!-- Order Information Row -->
    <div class="col-md-6">
        <!-- Payment Info and Order Summary Card -->
        <div class="card h-100">
            <div class="card-header bg-white py-2">
                <h6 class="mb-0">Payment & Summary</h6>
            </div>
            <div class="card-body p-2">
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <span class="text-muted small">Method:</span>
                    </div>
                    <div class="col-6 text-end small">
                        {{ ucfirst($order->payment_method) }}
                    </div>
                    <div class="col-6">
                        <span class="text-muted small">Status:</span>
                    </div>
                    <div class="col-6 text-end small">
                        @if($order->payment_status === 'paid')
                            <span class="text-success">Paid</span>
                        @else
                            <span class="text-warning">{{ ucfirst($order->payment_status) }}</span>
                        @endif
                    </div>
                </div>
                <hr class="my-2">
                <div class="row g-2">
                    <div class="col-6">
                        <span class="text-muted small">Subtotal:</span>
                    </div>
                    <div class="col-6 text-end small">
                        {{ config('app.currency_symbol') }}{{ number_format($order->total_amount - $order->shipping_cost, 2) }}
                    </div>
                    <div class="col-6">
                        <span class="text-muted small">Shipping:</span>
                    </div>
                    <div class="col-6 text-end small">
                        {{ $order->shipping_cost > 0 ? config('app.currency_symbol') . number_format($order->shipping_cost, 2) : 'Free' }}
                    </div>
                    <div class="col-6">
                        <span class="fw-bold small">Total:</span>
                    </div>
                    <div class="col-6 text-end">
                        <span class="fw-bold small">{{ config('app.currency_symbol') }}{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Addresses Column -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-white py-2">
                <h6 class="mb-0">Addresses</h6>
            </div>
            <div class="card-body p-2">
                <div class="mb-2">
                    <h6 class="small fw-bold mb-1">Shipping Address</h6>
                    <address class="mb-0 small">
                        @if($order->shippingAddress)
                            {{ $order->shippingAddress->name }}<br>
                            {{ $order->shippingAddress->phone }}<br>
                            {{ $order->shippingAddress->street_address }}<br>
                            {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->postal_code }}<br>
                            {{ $order->shippingAddress->country }}
                        @else
                            No shipping address found
                        @endif
                    </address>
                </div>
                <hr class="my-2">
                <div>
                    <h6 class="small fw-bold mb-1">Billing Address</h6>
                    <address class="mb-0 small">
                        @if($order->billingAddress)
                            {{ $order->billingAddress->name }}<br>
                            {{ $order->billingAddress->phone }}<br>
                            {{ $order->billingAddress->street_address }}<br>
                            {{ $order->billingAddress->city }}, {{ $order->billingAddress->state }} {{ $order->billingAddress->postal_code }}<br>
                            {{ $order->billingAddress->country }}
                        @else
                            No billing address found
                        @endif
                    </address>
                </div>
            </div>
        </div>
    </div>
</div> 