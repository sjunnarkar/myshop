@props(['cart'])

<div class="dropdown">
    <button class="btn btn-link text-dark position-relative p-0" 
        type="button" 
        data-bs-toggle="dropdown" 
        aria-expanded="false">
        <i class="bi bi-cart fs-5"></i>
        @if($cart && $cart->count() > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                {{ $cart->count() }}
            </span>
        @endif
    </button>
    <div class="dropdown-menu dropdown-menu-end p-0" style="width: 320px;">
        @if($cart && $cart->count() > 0)
            <div class="p-3">
                <h6 class="mb-3">Cart Items</h6>
                <div class="mini-cart-items" style="max-height: 300px; overflow-y: auto;">
                    @foreach($cart as $item)
                        <div class="mini-cart-item mb-3">
                            <div class="d-flex">
                                <!-- Product Image -->
                                <div class="flex-shrink-0" style="width: 60px;">
                                    <img src="{{ Storage::url($item->product->thumbnail) }}" 
                                        alt="{{ $item->product->name }}"
                                        class="img-fluid rounded"
                                        style="height: 60px; width: 60px; object-fit: cover;">
                                </div>
                                
                                <!-- Product Details -->
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 fs-6">{{ $item->product->name }}</h6>
                                    <p class="mb-0 small">
                                        <span class="text-muted">Qty: {{ $item->quantity }}</span>
                                        <span class="float-end fw-bold">
                                            {{ config('app.currency_symbol') }}{{ number_format($item->product->price * $item->quantity, 2) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        @if(!$loop->last)
                            <hr class="my-3">
                        @endif
                    @endforeach
                </div>
                
                <hr>
                
                <!-- Cart Summary -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span class="fw-bold">{{ config('app.currency_symbol') }}{{ number_format($cart->sum(function($item) { 
                            return $item->product->price * $item->quantity; 
                        }), 2) }}</span>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="d-grid gap-2">
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-primary btn-sm">
                        View Cart
                    </a>
                    <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-sm">
                        Checkout
                    </a>
                </div>
            </div>
        @else
            <div class="p-3 text-center">
                <i class="bi bi-cart text-muted mb-2" style="font-size: 2rem;"></i>
                <p class="mb-0 text-muted">Your cart is empty</p>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
.mini-cart-items::-webkit-scrollbar {
    width: 6px;
}

.mini-cart-items::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.mini-cart-items::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.mini-cart-items::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
@endpush 