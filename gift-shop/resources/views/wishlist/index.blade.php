@extends('layouts.shop')

@section('title', 'My Wishlists')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">My Wishlists</h1>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createWishlistModal">
                    <i class="fas fa-plus me-2"></i> Create New Wishlist
                </button>
            </div>

            @if($wishlists->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-heart text-muted mb-3" style="font-size: 3rem;"></i>
                    <h2 class="h4 mb-3">No Wishlists Yet</h2>
                    <p class="text-muted mb-4">Create your first wishlist to start saving your favorite items.</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createWishlistModal">
                        Create Your First Wishlist
                    </button>
                </div>
            @else
                <div class="row g-4">
                    @foreach($wishlists as $wishlist)
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h5 class="card-title mb-1">{{ $wishlist->name }}</h5>
                                            <p class="text-muted small mb-0">
                                                {{ $wishlist->items_count }} {{ Str::plural('item', $wishlist->items_count) }}
                                            </p>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-link text-dark p-0" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('wishlist.show', $wishlist) }}">
                                                        <i class="fas fa-eye me-2"></i> View
                                                    </a>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item" type="button" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#editWishlistModal" 
                                                            data-wishlist-id="{{ $wishlist->id }}"
                                                            data-wishlist-name="{{ $wishlist->name }}">
                                                        <i class="fas fa-edit me-2"></i> Edit
                                                    </button>
                                                </li>
                                                <li>
                                                    <form action="{{ route('wishlist.destroy', $wishlist) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger" 
                                                                onclick="return confirm('Are you sure you want to delete this wishlist?')">
                                                            <i class="fas fa-trash-alt me-2"></i> Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    @if($wishlist->items->isNotEmpty())
                                        <div class="row g-2 mb-3">
                                            @foreach($wishlist->items->take(4) as $item)
                                                <div class="col-3">
                                                    <img src="{{ $item->product->image_url }}" 
                                                         alt="{{ $item->product->name }}" 
                                                         class="img-fluid rounded">
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted small mb-3">No items in this wishlist yet.</p>
                                    @endif

                                    <a href="{{ route('wishlist.show', $wishlist) }}" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Create Wishlist Modal -->
<div class="modal fade" id="createWishlistModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Wishlist</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('wishlist.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Wishlist Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Wishlist</button>
                </div>
            </form>
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
            <form id="editWishlistForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Wishlist Name</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const editModal = document.getElementById('editWishlistModal');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const wishlistId = button.getAttribute('data-wishlist-id');
            const wishlistName = button.getAttribute('data-wishlist-name');
            
            const form = this.querySelector('#editWishlistForm');
            const nameInput = this.querySelector('#edit_name');
            
            form.action = `/wishlists/${wishlistId}`;
            nameInput.value = wishlistName;
        });
    }
});
</script>
@endpush
@endsection 