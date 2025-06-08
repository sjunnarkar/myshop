@extends('layouts.shop')

@section('title', 'Checkout')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form id="checkout-form" action="{{ route('checkout.process') }}" method="POST">
                @csrf
                
                <!-- Get current shipping and billing addresses -->
                @php
                    $currentShippingAddress = $addresses->where('is_shipping', true)->first();
                    $currentBillingAddress = $addresses->where('is_billing', true)->first();
                @endphp

                <!-- Shipping Address Section -->
                <div class="card mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Shipping Address</h5>
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#changeShippingAddressModal">
                            Change Address
                        </button>
                    </div>
                    <div class="card-body">
                        @if($currentShippingAddress)
                            <input type="hidden" name="shipping_address_id" value="{{ $currentShippingAddress->id }}">
                            <div id="shipping-address-display">
                                <strong>{{ $currentShippingAddress->name }}</strong>
                                <span class="badge bg-secondary">{{ ucfirst($currentShippingAddress->address_type) }}</span><br>
                                {{ $currentShippingAddress->phone }}<br>
                                {{ $currentShippingAddress->street_address }}<br>
                                {{ $currentShippingAddress->city }}, {{ $currentShippingAddress->state }} {{ $currentShippingAddress->postal_code }}<br>
                                {{ $currentShippingAddress->country }}
                            </div>
                        @else
                            <p class="text-muted mb-0">No shipping address selected</p>
                        @endif
                    </div>
                </div>

                <!-- Billing Address Section -->
                <div class="card mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Billing Address</h5>
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#changeBillingAddressModal">
                            Change Address
                        </button>
                    </div>
                    <div class="card-body">
                        @if($currentBillingAddress)
                            <input type="hidden" name="billing_address_id" value="{{ $currentBillingAddress->id }}">
                            <div id="billing-address-display">
                                <strong>{{ $currentBillingAddress->name }}</strong>
                                <span class="badge bg-secondary">{{ ucfirst($currentBillingAddress->address_type) }}</span><br>
                                {{ $currentBillingAddress->phone }}<br>
                                {{ $currentBillingAddress->street_address }}<br>
                                {{ $currentBillingAddress->city }}, {{ $currentBillingAddress->state }} {{ $currentBillingAddress->postal_code }}<br>
                                {{ $currentBillingAddress->country }}
                            </div>
                        @else
                            <p class="text-muted mb-0">No billing address selected</p>
                        @endif
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Payment Method</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="form-check mb-2">
                                <input class="form-check-input @error('payment_method') is-invalid @enderror" 
                                    type="radio" 
                                    name="payment_method" 
                                    id="payment_test" 
                                    value="test"
                                    checked>
                                <label class="form-check-label" for="payment_test">
                                    Test Payment (Stripe)
                                </label>
                            </div>
                            @error('payment_method')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Credit Card Details -->
                        <div id="credit-card-fields">
                            <div class="mb-3">
                                <label for="card_number" class="form-label">Card Number</label>
                                <input type="text" 
                                    class="form-control @error('card_number') is-invalid @enderror" 
                                    id="card_number" 
                                    name="card_number"
                                    value="4242424242424242"
                                    maxlength="16"
                                    required>
                                @error('card_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="card_expiry" class="form-label">Expiry Date</label>
                                    <input type="text" 
                                        class="form-control @error('card_expiry') is-invalid @enderror" 
                                        id="card_expiry" 
                                        name="card_expiry"
                                        value="12/25"
                                        placeholder="MM/YY"
                                        maxlength="5"
                                        required>
                                    @error('card_expiry')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="card_cvv" class="form-label">CVV</label>
                                    <input type="text" 
                                        class="form-control @error('card_cvv') is-invalid @enderror" 
                                        id="card_cvv" 
                                        name="card_cvv"
                                        value="123"
                                        maxlength="4"
                                        required>
                                    @error('card_cvv')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="alert alert-info">
                                <small><i class="bi bi-info-circle me-1"></i> This is a test payment mode. No actual payment will be processed.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Notes -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Order Notes</h5>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control" 
                            name="notes" 
                            rows="3" 
                            placeholder="Special instructions for delivery (optional)">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </form>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <!-- Cart Items -->
                    <div class="mb-4">
                        @foreach($cartItems as $item)
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0" style="width: 60px;">
                                    <img src="{{ Storage::url($item->product->thumbnail) }}" 
                                        alt="{{ $item->product->name }}"
                                        class="img-fluid rounded"
                                        style="height: 60px; width: 60px; object-fit: cover;">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 fs-6">{{ $item->product->name }}</h6>
                                    <p class="mb-0 small">
                                        <span class="text-muted">Qty: {{ $item->quantity }}</span>
                                        <span class="float-end">
                                            {{ \App\Helpers\CurrencyHelper::format($item->product->base_price * $item->quantity) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <hr>

                    <!-- Totals -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span>{{ \App\Helpers\CurrencyHelper::format($subtotal) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping</span>
                            <span>{{ $shipping > 0 ? \App\Helpers\CurrencyHelper::format($shipping) : 'Free' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax</span>
                            <span>{{ \App\Helpers\CurrencyHelper::format($tax) }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">Total</span>
                            <span class="fw-bold">{{ \App\Helpers\CurrencyHelper::format($total) }}</span>
                        </div>
                    </div>

                    <!-- Place Order Button -->
                    <div class="d-grid">
                        <button type="submit" 
                            form="checkout-form" 
                            class="btn btn-primary"
                            id="place-order-btn">
                            Place Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Address Change Modals -->
<div class="modal fade" id="changeShippingAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Shipping Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="address-list" style="max-height: 400px; overflow-y: auto;">
                    @foreach($addresses->where('id', '!=', optional($currentShippingAddress)->id) as $address)
                        <div class="form-check mb-3 p-3 border rounded">
                            <input class="form-check-input shipping-address-radio" 
                                type="radio" 
                                name="shipping_address_select" 
                                id="shipping_{{ $address->id }}" 
                                value="{{ $address->id }}"
                                onchange="updateShippingAddress(this)">
                            <label class="form-check-label w-100" for="shipping_{{ $address->id }}">
                                <strong>{{ $address->name }}</strong>
                                <span class="badge bg-secondary">{{ ucfirst($address->address_type) }}</span><br>
                                {{ $address->phone }}<br>
                                {{ $address->street_address }}<br>
                                {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}<br>
                                {{ $address->country }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="changeBillingAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Billing Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="address-list" style="max-height: 400px; overflow-y: auto;">
                    @foreach($addresses->where('id', '!=', optional($currentBillingAddress)->id) as $address)
                        <div class="form-check mb-3 p-3 border rounded">
                            <input class="form-check-input billing-address-radio" 
                                type="radio" 
                                name="billing_address_select" 
                                id="billing_{{ $address->id }}" 
                                value="{{ $address->id }}"
                                onchange="updateBillingAddress(this)">
                            <label class="form-check-label w-100" for="billing_{{ $address->id }}">
                                <strong>{{ $address->name }}</strong>
                                <span class="badge bg-secondary">{{ ucfirst($address->address_type) }}</span><br>
                                {{ $address->phone }}<br>
                                {{ $address->street_address }}<br>
                                {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}<br>
                                {{ $address->country }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkoutForm = document.getElementById('checkout-form');
    const placeOrderBtn = document.getElementById('place-order-btn');

    // Prevent multiple form submissions
    checkoutForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate shipping address
        const shippingAddressId = document.querySelector('input[name="shipping_address_id"]');
        if (!shippingAddressId || !shippingAddressId.value) {
            alert('Please select a shipping address');
            return;
        }

        // Validate billing address
        const billingAddressId = document.querySelector('input[name="billing_address_id"]');
        if (!billingAddressId || !billingAddressId.value) {
            alert('Please select a billing address');
            return;
        }

        // Disable button and show loading state
        placeOrderBtn.disabled = true;
        placeOrderBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

        // Submit the form
        this.submit();
    });
});

function updateShippingAddress(radio) {
    const formData = new FormData();
    const token = document.querySelector('meta[name="csrf-token"]').content;
    
    formData.append('_token', token);
    formData.append('address_id', radio.value);
    formData.append('is_shipping', '1');
    
    radio.disabled = true;
    
    fetch('{{ route('profile.addresses.update-preferences') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the hidden input field
            const shippingAddressInput = document.querySelector('input[name="shipping_address_id"]');
            shippingAddressInput.value = radio.value;
            
            // Close the modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('changeShippingAddressModal'));
            modal.hide();
            
            // Update the displayed address
            const addressLabel = radio.nextElementSibling.innerHTML;
            const displayArea = document.querySelector('#shipping-address-display');
            if (displayArea) {
                displayArea.innerHTML = addressLabel;
            }
            
            radio.disabled = false;
        } else {
            radio.disabled = false;
            alert(data.message || 'Failed to update shipping address');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        radio.disabled = false;
        alert('An error occurred while updating the shipping address');
    });
}

function updateBillingAddress(radio) {
    const formData = new FormData();
    const token = document.querySelector('meta[name="csrf-token"]').content;
    
    formData.append('_token', token);
    formData.append('address_id', radio.value);
    formData.append('is_billing', '1');
    
    radio.disabled = true;
    
    fetch('{{ route('profile.addresses.update-preferences') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the hidden input field
            const billingAddressInput = document.querySelector('input[name="billing_address_id"]');
            billingAddressInput.value = radio.value;
            
            // Close the modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('changeBillingAddressModal'));
            modal.hide();
            
            // Update the displayed address
            const addressLabel = radio.nextElementSibling.innerHTML;
            const displayArea = document.querySelector('#billing-address-display');
            if (displayArea) {
                displayArea.innerHTML = addressLabel;
            }
            
            radio.disabled = false;
        } else {
            radio.disabled = false;
            alert(data.message || 'Failed to update billing address');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        radio.disabled = false;
        alert('An error occurred while updating the billing address');
    });
}
</script>
@endpush 