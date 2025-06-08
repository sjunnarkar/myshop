@props(['product'])

@auth
    @php
        $inWishlist = auth()->user()->hasProductInWishlist($product->id);
        $wishlists = auth()->user()->wishlists;
        $productWishlist = $inWishlist ? auth()->user()->getWishlistContainingProduct($product->id) : null;
    @endphp

    @if($inWishlist)
        <a href="{{ route('wishlist.show', $productWishlist) }}" 
           class="btn btn-lg btn-light border wishlist-btn d-flex align-items-center justify-content-center gap-2 w-100">
            <i class="bi bi-heart-fill text-danger"></i> See in Wishlist
        </a>
    @else
        <button type="button" 
                class="btn btn-lg btn-light border wishlist-btn d-flex align-items-center justify-content-center gap-2 w-100" 
                data-bs-toggle="modal" 
                data-bs-target="#wishlistModal{{ $product->id }}">
            <i class="bi bi-heart"></i> Add to Wishlist
        </button>

        <!-- Wishlist Modal -->
        <div class="modal fade" id="wishlistModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fs-4">Add to Wishlist</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-2">
                        <form action="{{ route('wishlist.add-product') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            
                            <div class="mb-4">
                                <label for="wishlist_id" class="form-label">Choose Wishlist</label>
                                <select class="form-select form-select-lg" name="wishlist_id" id="wishlist_id">
                                    <option value="">My Wishlist (Default)</option>
                                    @foreach($wishlists as $wishlist)
                                        @if($wishlist->name !== 'My Wishlist')
                                            <option value="{{ $wishlist->id }}">{{ $wishlist->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="mb-4">
                                <label for="notes" class="form-label">Add a Note (Optional)</label>
                                <textarea class="form-control" 
                                    id="notes" 
                                    name="notes" 
                                    rows="2" 
                                    placeholder="e.g., Gift idea for Mom, Favorite color: blue"></textarea>
                                <div class="form-text">Add any special notes or reminders about this item</div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-lg btn-primary">
                                    <i class="bi bi-heart-fill me-2"></i> Add to Wishlist
                                </button>
                                <button type="button" class="btn btn-lg btn-light border" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@else
    <a href="{{ route('login') }}" 
       class="btn btn-lg btn-light border wishlist-btn d-flex align-items-center justify-content-center gap-2 w-100">
        <i class="bi bi-heart"></i> Add to Wishlist
    </a>
@endauth

<style>
    .wishlist-btn {
        transition: all 0.2s ease-in-out;
    }
    .wishlist-btn:not([disabled]):hover {
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }
    .wishlist-btn[disabled] {
        opacity: 1;
        background-color: #f8f9fa;
    }
    .wishlist-btn[disabled] .bi-heart-fill {
        animation: heartBeat 1.5s ease-in-out;
    }
    @keyframes heartBeat {
        0% { transform: scale(1); }
        14% { transform: scale(1.3); }
        28% { transform: scale(1); }
        42% { transform: scale(1.3); }
        70% { transform: scale(1); }
    }
</style> 