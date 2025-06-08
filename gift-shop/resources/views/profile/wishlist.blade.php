@extends('layouts.shop')

@section('title', 'My Wishlist')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle mb-3 text-muted">My Account</h6>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('profile.show') }}" class="list-group-item list-group-item-action">
                            <i class="bi bi-person me-2"></i> Profile
                        </a>
                        <a href="{{ route('profile.show') }}#addresses" class="list-group-item list-group-item-action">
                            <i class="bi bi-geo-alt me-2"></i> Addresses
                        </a>
                        <a href="{{ route('profile.show') }}#orders" class="list-group-item list-group-item-action">
                            <i class="bi bi-box me-2"></i> Orders
                        </a>
                        <a href="{{ route('profile.wishlist') }}" class="list-group-item list-group-item-action active">
                            <i class="bi bi-heart me-2"></i> Wishlist
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">My Wishlist</h5>
                </div>
                <div class="card-body">
                    @if($wishlist->count() > 0)
                        <div class="row row-cols-1 row-cols-md-3 g-4">
                            @foreach($wishlist as $item)
                                <div class="col">
                                    <div class="card h-100">
                                        <img src="{{ Storage::url($item->product->thumbnail) }}" 
                                            class="card-img-top" 
                                            alt="{{ $item->product->name }}"
                                            style="height: 200px; object-fit: cover;">
                                        <div class="card-body">
                                            <h6 class="card-title mb-2">{{ $item->product->name }}</h6>
                                            <p class="card-text text-primary mb-2">
                                                {{ config('app.currency_symbol') }}{{ number_format($item->product->price, 2) }}
                                            </p>
                                            <p class="card-text small text-muted mb-3">
                                                Added {{ $item->created_at->diffForHumans() }}
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <a href="{{ route('shop.show', $item->product) }}" 
                                                    class="btn btn-sm btn-outline-primary">
                                                    View Product
                                                </a>
                                                <form action="{{ route('profile.wishlist.remove') }}" 
                                                    method="POST" 
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                                    <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger"
                                                        title="Remove from wishlist">
                                                        <i class="bi bi-heart-fill"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            {{ $wishlist->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-heart display-4 text-muted mb-3"></i>
                            <h5>Your wishlist is empty</h5>
                            <p class="text-muted">Browse our products and add items to your wishlist!</p>
                            <a href="{{ route('shop.index') }}" class="btn btn-primary">
                                Browse Products
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 