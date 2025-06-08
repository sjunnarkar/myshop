@extends('layouts.shop')

@section('title', $wishlist->name)

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">{{ $wishlist->name }}</h1>
                    <p class="text-muted mb-0">
                        {{ $wishlist->items_count }} {{ Str::plural('item', $wishlist->items_count) }}
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#shareWishlistModal">
                        <i class="fas fa-share-alt me-2"></i> Share
                    </button>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editWishlistModal">
                        <i class="fas fa-edit me-2"></i> Edit
                    </button>
                </div>
            </div>

            @if($wishlist->items->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-heart text-muted mb-3" style="font-size: 3rem;"></i>
                    <h2 class="h4 mb-3">No Items Yet</h2>
                    <p class="text-muted mb-4">Start adding items to your wishlist while shopping.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary">
                        Browse Products
                    </a>
                </div>
            @else
                <div class="row g-4">
                    @foreach($wishlist->items as $item)
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="row g-0">
                                    <div class="col-4">
                                        <img src="{{ $item->product->image_url }}" 
                                             alt="{{ $item->product->name }}" 
                                             class="img-fluid rounded-start h-100 object-fit-cover">
                                    </div>
                                    <div class="col-8">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h5 class="card-title mb-1">
                                                        <a href="{{ route('products.show', $item->product) }}" class="text-decoration-none text-dark">
                                                            {{ $item->product->name }}
                                                        </a>
                                                    </h5>
                                                    <p class="card-text text-primary mb-2">
                                                        {{ config('app.currency_symbol') }}{{ number_format($item->product->base_price, 2) }}
                                                    </p>
                                                </div>
                                                <form action="{{ route('wishlist.remove-product', [$wishlist, $item->product]) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link text-danger p-0" 
                                                            onclick="return confirm('Remove this item from your wishlist?')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            
                                            @if($item->product->stock > 0)
                                                <form action="{{ route('cart.add') }}" method="POST" class="mt-3">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $item->product->id }}">
                                                    <button type="submit" class="btn btn-primary btn-sm w-100">
                                                        Add to Cart
                                                    </button>
                                                </form>
                                            @else
                                                <button class="btn btn-secondary btn-sm w-100 mt-3" disabled>
                                                    Out of Stock
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Edit Wishlist Modal -->
<div class="modal fade" id="editWishlistModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Wishlist</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('wishlist.update', $wishlist) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Wishlist Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $wishlist->name }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Share Wishlist Modal -->
<div class="modal fade" id="shareWishlistModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Share Wishlist</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">Share this wishlist with friends and family:</p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="shareUrl" value="{{ route('wishlist.show', $wishlist) }}" readonly>
                    <button class="btn btn-outline-secondary" type="button" onclick="copyShareUrl()">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <div class="d-flex justify-content-center gap-3">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('wishlist.show', $wishlist)) }}" 
                       target="_blank" 
                       class="btn btn-outline-primary">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('wishlist.show', $wishlist)) }}&text={{ urlencode('Check out my wishlist!') }}" 
                       target="_blank" 
                       class="btn btn-outline-info">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://wa.me/?text={{ urlencode('Check out my wishlist! ' . route('wishlist.show', $wishlist)) }}" 
                       target="_blank" 
                       class="btn btn-outline-success">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="mailto:?subject={{ urlencode('Check out my wishlist!') }}&body={{ urlencode('I thought you might like my wishlist: ' . route('wishlist.show', $wishlist)) }}" 
                       class="btn btn-outline-secondary">
                        <i class="fas fa-envelope"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyShareUrl() {
    const shareUrl = document.getElementById('shareUrl');
    shareUrl.select();
    document.execCommand('copy');
    
    const button = shareUrl.nextElementSibling;
    const originalIcon = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check"></i>';
    
    setTimeout(() => {
        button.innerHTML = originalIcon;
    }, 2000);
}
</script>
@endpush

<style>
.object-fit-cover {
    object-fit: cover;
}
</style>
@endsection 