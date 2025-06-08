@extends('layouts.shop')

@section('title', 'Shopping Cart')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8">
            <!-- Cart Items -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Shopping Cart ({{ count($cartItems) }} items)</h5>
                </div>
                <div class="card-body">
                    @if(count($cartItems) > 0)
                        @foreach($cartItems as $item)
                            <div class="row mb-4 cart-item" data-product-id="{{ $item['id'] }}">
                                <!-- Product Image -->
                                <div class="col-md-3 mb-3 mb-md-0">
                                    <img src="{{ Storage::url($item['product']->thumbnail) }}" 
                                        alt="{{ $item['product']->name }}"
                                        class="img-fluid rounded"
                                        style="height: 120px; width: 100%; object-fit: cover;">
                                </div>
                                
                                <!-- Product Details -->
                                <div class="col-md-9">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-2">{{ $item['product']->name }}</h6>
                                            <p class="text-muted small mb-2">
                                                Category: {{ $item['product']->category->name }}
                                            </p>
                                            @if(!empty($item['customization_details']))
                                                <p class="text-muted small mb-2">
                                                    Options: 
                                                    @foreach($item['customization_details'] as $key => $value)
                                                        <span class="me-2">{{ ucfirst($key) }}: {{ $value }}</span>
                                                    @endforeach
                                                </p>
                                            @endif
                                        </div>
                                        <form action="{{ route('cart.remove', $item['id']) }}" 
                                            method="POST" 
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                class="btn btn-outline-danger btn-sm"
                                                title="Remove from cart">
                                                <i class="bi bi-trash me-1"></i>
                                                Remove
                                            </button>
                                        </form>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div class="d-flex align-items-center">
                                            <!-- Quantity Controls -->
                                            <form action="{{ route('cart.update', $item['id']) }}" 
                                                method="POST" 
                                                class="quantity-form">
                                                @csrf
                                                @method('PATCH')
                                                <button type="button" 
                                                    class="btn btn-outline-secondary quantity-btn"
                                                    data-action="decrease">
                                                    <i class="bi bi-dash-lg"></i>
                                                </button>
                                                <input type="number" 
                                                    name="quantity" 
                                                    value="{{ $item['quantity'] }}"
                                                    min="1"
                                                    max="{{ $item['product']->stock }}"
                                                    class="form-control">
                                                <button type="button" 
                                                    class="btn btn-outline-secondary quantity-btn"
                                                    data-action="increase">
                                                    <i class="bi bi-plus-lg"></i>
                                                </button>
                                            </form>
                                        </div>
                                        
                                        <!-- Price -->
                                        <div class="text-end">
                                            <p class="mb-0 fw-bold">
                                                {{ \App\Helpers\CurrencyHelper::format($item['product']->base_price * $item['quantity']) }}
                                            </p>
                                            <small class="text-muted">
                                                {{ \App\Helpers\CurrencyHelper::format($item['product']->base_price) }} each
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if(!$loop->last)
                                <hr class="my-4">
                            @endif
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-cart display-4 text-muted mb-3"></i>
                            <h5>Your cart is empty</h5>
                            <p class="text-muted">Browse our products and start shopping!</p>
                            <a href="{{ route('products.index') }}" class="btn btn-primary">
                                Browse Products
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Continue Shopping -->
            <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left me-2"></i> Continue Shopping
            </a>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4">
            @if(count($cartItems) > 0)
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span id="cart-subtotal">{{ \App\Helpers\CurrencyHelper::format($subtotal ?? 0) }}</span>
                        </div>
                        @if(session('coupon'))
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>Discount ({{ session('coupon.code') }})</span>
                                <span>-{{ \App\Helpers\CurrencyHelper::format(session('coupon.discount')) }}</span>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping</span>
                            <span>{{ ($shipping ?? 0) > 0 ? \App\Helpers\CurrencyHelper::format($shipping) : 'Free' }}</span>
                        </div>
                        @if(($tax ?? 0) > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax</span>
                                <span>{{ \App\Helpers\CurrencyHelper::format($tax) }}</span>
                            </div>
                        @endif
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold">Total</span>
                            <span class="fw-bold" id="cart-total">{{ \App\Helpers\CurrencyHelper::format($total ?? 0) }}</span>
                        </div>

                        <!-- Coupon Code -->
                        <form action="{{ route('cart.coupon') }}" method="POST" class="mb-3">
                            @csrf
                            <div class="input-group">
                                <input type="text" 
                                    class="form-control @error('coupon_code') is-invalid @enderror" 
                                    name="coupon_code"
                                    placeholder="Coupon code"
                                    value="{{ old('coupon_code') }}">
                                <button class="btn btn-outline-secondary" type="submit">Apply</button>
                            </div>
                            @error('coupon_code')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @if(session('error'))
                                <div class="text-danger small mt-1">{{ session('error') }}</div>
                            @endif
                            @if(session('success'))
                                <div class="text-success small mt-1">{{ session('success') }}</div>
                            @endif
                        </form>

                        <!-- Checkout Button -->
                        <a href="{{ route('checkout.index') }}" class="btn btn-primary w-100">
                            Proceed to Checkout
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle quantity updates
    document.querySelectorAll('.quantity-form').forEach(form => {
        const input = form.querySelector('input[name="quantity"]');
        const decreaseBtn = form.querySelector('[data-action="decrease"]');
        const increaseBtn = form.querySelector('[data-action="increase"]');
        
        const updateQuantity = async (newValue) => {
            // Ensure the value is not negative
            if (newValue < 1) {
                input.value = 1;
                return;
            }

            const maxValue = parseInt(input.getAttribute('max'));
            if (newValue > maxValue) {
                input.value = maxValue;
                return;
            }

            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            formData.append('_method', 'PATCH');
            formData.append('quantity', newValue);
            formData.append('product_id', form.closest('.cart-item').dataset.productId);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();
                
                if (data.success) {
                    // Update the quantity input
                    input.value = newValue;
                    
                    // Update the product total price
                    const priceContainer = form.closest('.cart-item').querySelector('.text-end');
                    if (priceContainer) {
                        const totalPrice = priceContainer.querySelector('.fw-bold');
                        if (totalPrice) {
                            totalPrice.textContent = '₹' + (data.item_total || data.subtotal).toFixed(2);
                        }
                    }
                    
                    // Update cart totals
                    const cartSubtotal = document.getElementById('cart-subtotal');
                    const cartTotal = document.getElementById('cart-total');
                    
                    if (cartSubtotal && data.subtotal) {
                        cartSubtotal.textContent = '₹' + data.subtotal.toFixed(2);
                    }
                    if (cartTotal && data.total) {
                        cartTotal.textContent = '₹' + data.total.toFixed(2);
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                // Reset to previous valid value
                input.value = Math.max(1, parseInt(input.value) || 1);
            }
        };
        
        // Handle decrease button click
        decreaseBtn.addEventListener('click', () => {
            const currentValue = parseInt(input.value) || 1;
            if (currentValue > 1) {
                updateQuantity(currentValue - 1);
            }
        });
        
        // Handle increase button click
        increaseBtn.addEventListener('click', () => {
            const currentValue = parseInt(input.value) || 1;
            const maxValue = parseInt(input.getAttribute('max'));
            if (currentValue < maxValue) {
                updateQuantity(currentValue + 1);
            }
        });
        
        // Handle direct input
        input.addEventListener('input', function() {
            if (this.value < 0 || this.value === '-') {
                this.value = 1;
            }
        });
        
        // Handle input change
        input.addEventListener('change', function() {
            let value = parseInt(this.value) || 1;
            const max = parseInt(this.getAttribute('max'));
            // Ensure value is between 1 and max
            value = Math.min(Math.max(value, 1), max);
            this.value = value;
            updateQuantity(value);
        });
    });
});
</script>
@endpush 