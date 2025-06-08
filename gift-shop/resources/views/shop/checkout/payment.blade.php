@extends('layouts.shop')

@section('title', 'Payment')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title mb-4">Payment Method</h3>

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="payment-methods mb-4">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="razorpay" value="razorpay" checked>
                            <label class="form-check-label" for="razorpay">
                                Pay with Card (Razorpay)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal">
                            <label class="form-check-label" for="paypal">
                                PayPal
                            </label>
                        </div>
                    </div>

                    <div id="razorpay-button-container">
                        <button id="pay-with-razorpay" class="btn btn-primary">Pay with Razorpay</button>
                    </div>

                    <div id="paypal-button-container" style="display: none;"></div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title mb-4">Order Summary</h3>
                    <div class="order-summary">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>₹{{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax:</span>
                            <span>₹{{ number_format($order->tax, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span>₹{{ number_format($order->shipping_cost, 2) }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <strong>Total:</strong>
                            <strong>₹{{ number_format($order->total, 2) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="https://www.paypal.com/sdk/js?client-id={{ config('services.paypal.client_id') }}&currency=INR"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
        const razorpayContainer = document.getElementById('razorpay-button-container');
        const paypalContainer = document.getElementById('paypal-button-container');

        // Handle payment method selection
        paymentMethods.forEach(method => {
            method.addEventListener('change', function() {
                if (this.value === 'razorpay') {
                    razorpayContainer.style.display = 'block';
                    paypalContainer.style.display = 'none';
                } else {
                    razorpayContainer.style.display = 'none';
                    paypalContainer.style.display = 'block';
                }
            });
        });

        // Initialize Razorpay
        const razorpayButton = document.getElementById('pay-with-razorpay');
        razorpayButton.addEventListener('click', function() {
            fetch(`/payment/razorpay/order/{{ $order->id }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const options = {
                        key: '{{ config('services.razorpay.key') }}',
                        amount: data.amount,
                        currency: 'INR',
                        name: '{{ config('app.name') }}',
                        description: 'Order #{{ $order->id }}',
                        order_id: data.order_id,
                        handler: function(response) {
                            fetch(`/payment/razorpay/success/{{ $order->id }}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    razorpay_payment_id: response.razorpay_payment_id,
                                    razorpay_order_id: response.razorpay_order_id,
                                    razorpay_signature: response.razorpay_signature
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    window.location.href = '/orders/' + {{ $order->id }};
                                } else {
                                    alert('Payment failed: ' + data.message);
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('An error occurred while processing your payment.');
                            });
                        },
                        prefill: {
                            name: '{{ auth()->user()->name }}',
                            email: '{{ auth()->user()->email }}',
                            contact: '{{ auth()->user()->phone }}'
                        },
                        theme: {
                            color: '#3399cc'
                        }
                    };
                    const rzp = new Razorpay(options);
                    rzp.open();
                } else {
                    alert('Error creating order: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while creating your order.');
            });
        });

        // Initialize PayPal
        paypal.Buttons({
            createOrder: function(data, actions) {
                return fetch(`/payment/paypal/{{ $order->id }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        return data.order_id;
                    }
                    throw new Error(data.message);
                });
            },
            onApprove: function(data, actions) {
                return fetch(`/payment/paypal/{{ $order->id }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        paypal_order_id: data.orderID
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = '/orders/' + {{ $order->id }};
                    } else {
                        throw new Error(data.message);
                    }
                });
            },
            onError: function(err) {
                console.error('PayPal Error:', err);
                alert('An error occurred with your PayPal payment.');
            }
        }).render('#paypal-button-container');
    });
</script>
@endpush 