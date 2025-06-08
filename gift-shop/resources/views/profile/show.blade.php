@extends('layouts.shop')

@section('title', 'My Profile')

@section('content')
<div class="container py-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row profile-container">
        <!-- Profile Sidebar -->
        <div class="col-lg-3 profile-sidebar">
            <div class="sidebar-content">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        @if($user->avatar)
                            <img src="{{ Storage::url($user->avatar) }}" 
                                alt="{{ $user->name }}" 
                                class="rounded-circle mb-3"
                                style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto mb-3"
                                style="width: 150px; height: 150px;">
                                <i class="bi bi-person-fill text-white" style="font-size: 4rem;"></i>
                            </div>
                        @endif
                        <h5 class="mb-1">{{ $user->name }}</h5>
                        <p class="text-muted small mb-3">{{ $user->email }}</p>
                    </div>
                </div>

                <div class="list-group mb-4">
                    <a href="#profile" class="list-group-item list-group-item-action active" data-bs-toggle="list">
                        <i class="bi bi-person me-2"></i> Profile
                    </a>
                    <a href="#addresses" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="bi bi-geo-alt me-2"></i> Addresses
                    </a>
                    <a href="#orders" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="bi bi-box me-2"></i> Orders
                    </a>
                    <a href="#wishlist" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="bi bi-heart me-2"></i> Wishlist
                    </a>
                    <a href="#security" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="bi bi-shield-lock me-2"></i> Security
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9 profile-content">
            <div class="tab-content">
                <!-- Profile Tab -->
                <div class="tab-pane fade show active" id="profile">
                    <div class="card">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Profile Information</h5>
                            <button type="button" class="btn btn-link text-primary p-0" id="toggleEdit">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">
                                @csrf
                                @method('PATCH')

                                <!-- Avatar -->
                                <div class="text-center mb-4">
                                    @if($user->avatar)
                                        <img src="{{ Storage::url($user->avatar) }}" 
                                            alt="{{ $user->name }}" 
                                            class="rounded-circle mb-3"
                                            style="width: 150px; height: 150px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto mb-3"
                                            style="width: 150px; height: 150px;">
                                            <i class="bi bi-person-fill text-white" style="font-size: 4rem;"></i>
                                </div>
                                    @endif
                                    <div class="mb-3">
                                        <input type="file" 
                                            class="form-control @error('avatar') is-invalid @enderror" 
                                            id="avatar" 
                                            name="avatar"
                                            accept="image/*"
                                            disabled>
                                        @error('avatar')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Maximum file size: 2MB</div>
                            </div>
                                </div>

                                <!-- Name -->
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="profile_name" class="form-label">Name</label>
                                        <input type="text" class="form-control" id="profile_name" name="name" value="{{ $user->name }}" required disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="profile_phone" class="form-label">Phone</label>
                                        <input type="tel" class="form-control" id="profile_phone" name="phone" value="{{ $user->phone }}" required disabled>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 mt-3 d-none" id="formButtons">
                                    <button type="submit" class="btn btn-primary flex-grow-1">
                                        Save Changes
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary flex-grow-1" id="cancelEdit">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Addresses Tab -->
                <div class="tab-pane fade" id="addresses" role="tabpanel" aria-labelledby="addresses-tab">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Addresses</h5>
                            <button type="button" class="btn btn-primary" id="toggleAddressForm">
                                <i class="bi bi-plus-lg me-1"></i> Add New Address
                            </button>
                        </div>
                        <div class="card-body">
                            <!-- Address Form -->
                            <form action="{{ route('profile.addresses.store') }}" method="POST" id="addressForm" class="mb-4 d-none">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="address_name" class="form-label">Full Name</label>
                                        <input type="text" class="form-control" id="address_name" name="name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="address_type" class="form-label">Address Type</label>
                                        <select class="form-select" id="address_type" name="address_type" required>
                                            <option value="home">Home</option>
                                            <option value="office">Office</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label for="street_address" class="form-label">Street Address</label>
                                        <input type="text" class="form-control" id="street_address" name="street_address" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="city" class="form-label">City</label>
                                        <input type="text" class="form-control" id="city" name="city" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="state" class="form-label">State</label>
                                        <input type="text" class="form-control" id="state" name="state" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="postal_code" class="form-label">Postal Code</label>
                                        <input type="text" class="form-control" id="postal_code" name="postal_code" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="country" class="form-label">Country</label>
                                        <input type="text" class="form-control" id="country" name="country" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="address_phone" class="form-label">Phone Number</label>
                                        <input type="text" class="form-control" id="address_phone" name="phone" required>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="is_shipping" name="is_shipping" value="1">
                                            <label class="form-check-label" for="is_shipping">
                                                <span class="badge bg-light text-dark border">
                                                    <i class="bi bi-truck me-1"></i> Set as Default Shipping Address
                                                </span>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_billing" name="is_billing" value="1">
                                            <label class="form-check-label" for="is_billing">
                                                <span class="badge bg-light text-dark border">
                                                    <i class="bi bi-credit-card me-1"></i> Set as Default Billing Address
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">Save Address</button>
                                    <button type="button" class="btn btn-outline-secondary" id="cancelAddress">Cancel</button>
                                </div>
                            </form>

                            <!-- Address List -->
                            <div class="row">
                                @forelse($addresses as $address)
                                    <div class="col-md-6 mb-4">
                                            <div class="card h-100">
                                                <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <h5 class="card-title mb-0">
                                                        {{ ucfirst($address->address_type) }} Address
                                                    </h5>
                                                        <div class="dropdown">
                                                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                            Actions
                                                            </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                                <li>
                                                                <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editAddressModal{{ $address->id }}">
                                                                        <i class="bi bi-pencil me-2"></i> Edit
                                                                </button>
                                                                </li>
                                                                <li>
                                                                <form action="{{ route('profile.addresses.destroy', $address) }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this address?')">
                                                                            <i class="bi bi-trash me-2"></i> Delete
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                <p class="card-text mb-3">
                                                    <strong>{{ $address->name }}</strong><br>
                                                    {{ $address->street_address }}<br>
                                                    {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}<br>
                                                    {{ $address->country }}<br>
                                                    Phone: {{ $address->phone }}
                                                </p>
                                                <div class="d-flex flex-wrap gap-2 mb-3">
                                                    <form action="{{ route('profile.addresses.update', $address) }}" 
                                                        method="POST" 
                                                        class="w-100 address-preference-form"
                                                        data-address-id="{{ $address->id }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="address_type" value="{{ $address->address_type }}">
                                                        <input type="hidden" name="street_address" value="{{ $address->street_address }}">
                                                        <input type="hidden" name="city" value="{{ $address->city }}">
                                                        <input type="hidden" name="state" value="{{ $address->state }}">
                                                        <input type="hidden" name="postal_code" value="{{ $address->postal_code }}">
                                                        <input type="hidden" name="country" value="{{ $address->country }}">
                                                        <input type="hidden" name="phone" value="{{ $address->phone }}">
                                                        
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input preference-checkbox" 
                                                                type="checkbox" 
                                                                id="is_shipping_{{ $address->id }}" 
                                                                name="is_shipping" 
                                                                value="1" 
                                                                data-type="shipping"
                                                                {{ $address->is_shipping ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="is_shipping_{{ $address->id }}">
                                                                <span class="badge shipping-badge {{ $address->is_shipping ? 'bg-primary' : 'bg-light text-dark border' }}">
                                                                    <i class="bi bi-truck me-1"></i> Default Shipping Address
                                                                </span>
                                                            </label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input preference-checkbox" 
                                                                type="checkbox" 
                                                                id="is_billing_{{ $address->id }}" 
                                                                name="is_billing" 
                                                                value="1" 
                                                                data-type="billing"
                                                                {{ $address->is_billing ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="is_billing_{{ $address->id }}">
                                                                <span class="badge billing-badge {{ $address->is_billing ? 'bg-info' : 'bg-light text-dark border' }}">
                                                                    <i class="bi bi-credit-card me-1"></i> Default Billing Address
                                                                </span>
                                                            </label>
                                                    </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Edit Address Modal -->
                                    <div class="modal fade" id="editAddressModal{{ $address->id }}" tabindex="-1" aria-labelledby="editAddressModalLabel{{ $address->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editAddressModalLabel{{ $address->id }}">Edit Address</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('profile.addresses.update', $address) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="name{{ $address->id }}" class="form-label">Full Name</label>
                                                            <input type="text" class="form-control" id="name{{ $address->id }}" name="name" value="{{ $address->name }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="address_type{{ $address->id }}" class="form-label">Address Type</label>
                                                            <select class="form-select" id="address_type{{ $address->id }}" name="address_type" required>
                                                                <option value="home" {{ $address->address_type === 'home' ? 'selected' : '' }}>Home</option>
                                                                <option value="office" {{ $address->address_type === 'office' ? 'selected' : '' }}>Office</option>
                                                                <option value="other" {{ $address->address_type === 'other' ? 'selected' : '' }}>Other</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="street_address{{ $address->id }}" class="form-label">Street Address</label>
                                                            <input type="text" class="form-control" id="street_address{{ $address->id }}" name="street_address" value="{{ $address->street_address }}" required>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6 mb-3">
                                                                <label for="city{{ $address->id }}" class="form-label">City</label>
                                                                <input type="text" class="form-control" id="city{{ $address->id }}" name="city" value="{{ $address->city }}" required>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label for="state{{ $address->id }}" class="form-label">State</label>
                                                                <input type="text" class="form-control" id="state{{ $address->id }}" name="state" value="{{ $address->state }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6 mb-3">
                                                                <label for="postal_code{{ $address->id }}" class="form-label">Postal Code</label>
                                                                <input type="text" class="form-control" id="postal_code{{ $address->id }}" name="postal_code" value="{{ $address->postal_code }}" required>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label for="country{{ $address->id }}" class="form-label">Country</label>
                                                                <input type="text" class="form-control" id="country{{ $address->id }}" name="country" value="{{ $address->country }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="phone{{ $address->id }}" class="form-label">Phone Number</label>
                                                            <input type="text" class="form-control" id="phone{{ $address->id }}" name="phone" value="{{ $address->phone }}" required>
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
                                @empty
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            No addresses found. Add your first address above.
                                        </div>
                                    </div>
                                @endforelse
                                </div>
                        </div>
                    </div>
                </div>

                <!-- Orders Tab -->
                <div class="tab-pane fade" id="orders">
                    <!-- Orders List View -->
                    <div id="orders-list-view">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Order History</h5>
                            </div>
                            <div class="card-body">
                                @if($orders->isEmpty())
                                    <p class="text-center text-muted my-4">
                                        You haven't placed any orders yet.
                                    </p>
                                @else
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Order #</th>
                                                    <th>Date</th>
                                                    <th>Status</th>
                                                    <th>Total</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($orders as $order)
                                                    <tr>
                                                        <td>{{ $order->order_number }}</td>
                                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $order->status_color }}">
                                                                {{ ucfirst($order->status) }}
                                                            </span>
                                                        </td>
                                                        <td>{{ config('app.currency_symbol') }}{{ number_format($order->total_amount, 2) }}</td>
                                                        <td>
                                                            <a href="#" class="btn btn-sm btn-outline-primary view-order-details" data-order-id="{{ $order->id }}">View Details</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center p-3 border-top">
                                        <div class="small text-muted">
                                            Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} orders
                                        </div>
                                        <nav aria-label="Orders navigation">
                                            {{ $orders->onEachSide(1)->links('pagination::bootstrap-5') }}
                                        </nav>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Order Details View -->
                    <div id="order-details-view" class="d-none">
                        <div class="card">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Order Details</h5>
                                <button type="button" class="btn btn-sm btn-outline-secondary back-to-orders">
                                    <i class="bi bi-arrow-left"></i> Back to Orders
                                </button>
                            </div>
                            <div class="card-body" id="order-details-content">
                                <div class="text-center py-5">
                                    <p class="text-muted">Select an order to view details</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Wishlist Tab -->
                <div class="tab-pane fade" id="wishlist">
                    <div class="card">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">My Wishlists</h5>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createWishlistModal">
                                <i class="bi bi-plus-lg me-1"></i> Create New Wishlist
                            </button>
                        </div>
                        <div class="card-body">
                            @if($wishlists->isEmpty())
                                <p class="text-center text-muted my-4">
                                    You don't have any wishlists yet.
                                </p>
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
                                                                <i class="bi bi-three-dots-vertical"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li>
                                                                    <a class="dropdown-item" href="{{ route('wishlist.show', $wishlist) }}">
                                                                        <i class="bi bi-eye me-2"></i> View
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <button class="dropdown-item" type="button" 
                                                                            data-bs-toggle="modal" 
                                                                            data-bs-target="#editWishlistModal" 
                                                                            data-wishlist-id="{{ $wishlist->id }}"
                                                                            data-wishlist-name="{{ $wishlist->name }}">
                                                                        <i class="bi bi-pencil me-2"></i> Edit
                                                                    </button>
                                                                </li>
                                                                <li>
                                                                    <form action="{{ route('wishlist.destroy', $wishlist) }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="dropdown-item text-danger" 
                                                                                onclick="return confirm('Are you sure you want to delete this wishlist?')">
                                                                            <i class="bi bi-trash me-2"></i> Delete
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
                                                                    @if($item->product->thumbnail)
                                                                        <img src="{{ Storage::url($item->product->thumbnail) }}" 
                                                                             alt="{{ $item->product->name }}" 
                                                                             class="img-fluid rounded"
                                                                             style="height: 80px; object-fit: cover;">
                                                                    @else
                                                                        <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                                                             style="height: 80px;">
                                                                            <i class="bi bi-image text-muted"></i>
                                                                        </div>
                                                                    @endif
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

                <!-- Security Tab -->
                <div class="tab-pane fade" id="security">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Security Settings</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('profile.password.update') }}" method="POST">
                                @csrf
                                @method('PATCH')

                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" 
                                        class="form-control @error('current_password') is-invalid @enderror" 
                                        id="current_password" 
                                        name="current_password"
                                        required>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password" 
                                        class="form-control @error('password') is-invalid @enderror" 
                                        id="password" 
                                        name="password"
                                        required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                    <input type="password" 
                                        class="form-control" 
                                        id="password_confirmation" 
                                        name="password_confirmation"
                                        required>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        Update Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to activate a tab
    function activateTab(tabId) {
        const tab = document.querySelector(`a[href="${tabId}"]`);
        if (tab) {
            const bsTab = new bootstrap.Tab(tab);
            bsTab.show();
        }
    }

    // Function to scroll to top
    function scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    // Handle tab activation from URL hash
    function handleTabFromHash() {
        const hash = window.location.hash;
        if (hash) {
            // Delay tab activation slightly to ensure DOM is ready
            setTimeout(() => {
                activateTab(hash);
                scrollToTop(); // Always scroll to top when hash changes
            }, 150);
        }
    }

    // Initial load
    handleTabFromHash();

    // Listen for hash changes (for navigation from other pages)
    window.addEventListener('hashchange', handleTabFromHash);

    // Listen for tab changes
    document.querySelectorAll('a[data-bs-toggle="list"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function (e) {
            const targetId = e.target.getAttribute('href');
            // Update URL hash without scrolling
            history.pushState(null, null, targetId);
            // Always scroll to top when tab changes
            scrollToTop();
        });
    });

    // Handle top navigation links (Profile and Orders)
    const topNavLinks = document.querySelectorAll('a[href="{{ route("profile.show") }}"], a[href="{{ route("profile.show") }}#profile"], a[href="{{ route("profile.show") }}#orders"]');
    topNavLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (window.location.pathname === '{{ route("profile.show") }}') {
                e.preventDefault();
                const hash = this.getAttribute('href').includes('#') ? '#' + this.getAttribute('href').split('#')[1] : '#profile';
                
                // Always scroll to top, even if we're already on the tab
                scrollToTop();
                
                // Only activate tab if we're not already on it
                const currentTab = document.querySelector('.tab-pane.active');
                if (!currentTab || currentTab.id !== hash.substring(1)) {
                    activateTab(hash);
                }
            }
        });
    });

    // Profile Form Toggle
    const toggleEdit = document.getElementById('toggleEdit');
    const profileForm = document.getElementById('profileForm');
    const formButtons = document.getElementById('formButtons');
    const cancelEdit = document.getElementById('cancelEdit');

    // Function to enable edit mode
    function enableEditMode() {
        const inputs = profileForm.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.disabled = false;
        });
        toggleEdit.classList.add('d-none');
        formButtons.classList.remove('d-none');
    }

    // Function to disable edit mode
    function disableEditMode() {
        const inputs = profileForm.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.disabled = true;
        });
        toggleEdit.classList.remove('d-none');
        formButtons.classList.add('d-none');
    }

    // Toggle edit button click handler
    toggleEdit.addEventListener('click', function(e) {
        e.preventDefault();
        enableEditMode();
    });

    // Cancel button click handler
    cancelEdit.addEventListener('click', function(e) {
        e.preventDefault();
        // Reset form to original values
        profileForm.reset();
        disableEditMode();
    });

    // Form submission handler
    profileForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get the form data
        const formData = new FormData(profileForm);
        
        // Send the form data using fetch
        fetch(profileForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(errors => {
                    throw new Error(JSON.stringify(errors));
                });
            }
            return response.text();
        })
        .then(data => {
            // Handle successful submission
            disableEditMode();
            // Show success message and reload
            window.location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            try {
                const errors = JSON.parse(error.message);
                let errorMessage = 'Please fix the following errors:\n';
                if (errors.errors) {
                    Object.entries(errors.errors).forEach(([field, messages]) => {
                        errorMessage += `- ${field}: ${messages.join(', ')}\n`;
                    });
                } else if (errors.message) {
                    errorMessage = errors.message;
                }
                alert(errorMessage);
            } catch (e) {
                alert('There was an error saving your profile. Please try again.');
            }
        });
    });

    // Address Form Toggle
    const toggleAddressForm = document.getElementById('toggleAddressForm');
    const addressForm = document.getElementById('addressForm');
    const cancelAddress = document.getElementById('cancelAddress');

    // Function to show address form
    function showAddressForm() {
        addressForm.classList.remove('d-none');
        toggleAddressForm.classList.add('d-none');
    }

    // Function to hide address form
    function hideAddressForm() {
        addressForm.classList.add('d-none');
        toggleAddressForm.classList.remove('d-none');
        // Reset form
        addressForm.reset();
    }

    // Toggle address form button click handler
    toggleAddressForm.addEventListener('click', function(e) {
        e.preventDefault();
        showAddressForm();
    });

    // Cancel address button click handler
    cancelAddress.addEventListener('click', function(e) {
        e.preventDefault();
        hideAddressForm();
    });

    // Address form submission handler
    addressForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get the form data
        const formData = new FormData(addressForm);
        
        // Send the form data using fetch
        fetch(addressForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(errors => {
                    throw new Error(JSON.stringify(errors));
                });
            }
            return response.text();
        })
        .then(data => {
            // Handle successful submission
            hideAddressForm();
            // Reload the page to show the success message from the server
            window.location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            try {
                const errors = JSON.parse(error.message);
                let errorMessage = 'Please fix the following errors:\n';
                if (errors.errors) {
                    Object.entries(errors.errors).forEach(([field, messages]) => {
                        errorMessage += `- ${field}: ${messages.join(', ')}\n`;
                    });
                } else if (errors.message) {
                    errorMessage = errors.message;
                }
                alert(errorMessage);
            } catch (e) {
                alert('There was an error saving the address. Please try again.');
            }
        });
    });

    // Handle address preference changes
    document.querySelectorAll('.preference-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function(e) {
            const form = this.closest('form');
            const addressId = form.dataset.addressId;
            const type = this.dataset.type;
            const isChecked = this.checked;
            const badge = this.closest('.form-check').querySelector(`.${type}-badge`);
            
            // Prepare form data
            const formData = new FormData(form);
            
            // Show loading state
            badge.style.opacity = '0.5';
            this.disabled = true;

            // Send AJAX request
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the current badge
                    if (isChecked) {
                        badge.classList.remove('bg-light', 'text-dark', 'border');
                        badge.classList.add(type === 'shipping' ? 'bg-primary' : 'bg-info');
                    } else {
                        badge.classList.add('bg-light', 'text-dark', 'border');
                        badge.classList.remove(type === 'shipping' ? 'bg-primary' : 'bg-info');
                    }

                    // Update other addresses' badges if this one was set as default
                    if (isChecked) {
                        document.querySelectorAll(`.address-preference-form:not([data-address-id="${addressId}"]) .preference-checkbox[data-type="${type}"]`).forEach(otherCheckbox => {
                            otherCheckbox.checked = false;
                            const otherBadge = otherCheckbox.closest('.form-check').querySelector(`.${type}-badge`);
                            otherBadge.classList.add('bg-light', 'text-dark', 'border');
                            otherBadge.classList.remove(type === 'shipping' ? 'bg-primary' : 'bg-info');
                        });
                    }

                    // Show success message
                    const toast = new bootstrap.Toast(document.createElement('div'));
                    toast.element.classList.add('toast', 'position-fixed', 'bottom-0', 'end-0', 'm-3');
                    toast.element.innerHTML = `
                        <div class="toast-header bg-success text-white">
                            <strong class="me-auto">Success</strong>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                        </div>
                        <div class="toast-body">
                            Address preferences updated successfully
                        </div>
                    `;
                    document.body.appendChild(toast.element);
                    toast.show();
                    setTimeout(() => toast.element.remove(), 3000);
                } else {
                    // Revert the checkbox if there was an error
                    this.checked = !isChecked;
                    throw new Error(data.message || 'Failed to update address preferences');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Show error message
                const toast = new bootstrap.Toast(document.createElement('div'));
                toast.element.classList.add('toast', 'position-fixed', 'bottom-0', 'end-0', 'm-3');
                toast.element.innerHTML = `
                    <div class="toast-header bg-danger text-white">
                        <strong class="me-auto">Error</strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body">
                        ${error.message || 'Failed to update address preferences'}
                    </div>
                `;
                document.body.appendChild(toast.element);
                toast.show();
                setTimeout(() => toast.element.remove(), 3000);
            })
            .finally(() => {
                // Reset loading state
                badge.style.opacity = '1';
                this.disabled = false;
            });
        });
    });

    // Ensure buttons are hidden on page load
    disableEditMode();
    hideAddressForm();

    // Edit Wishlist Modal
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

    // Order Details Functionality
    const viewOrderDetailsButtons = document.querySelectorAll('.view-order-details');
    const ordersListView = document.getElementById('orders-list-view');
    const orderDetailsView = document.getElementById('order-details-view');
    const orderDetailsContent = document.getElementById('order-details-content');
    const backToOrdersButton = document.querySelector('.back-to-orders');
    const ordersSidebarLink = document.querySelector('a[href="#orders"]');

    // Function to show orders list
    function showOrdersList() {
        ordersListView.classList.remove('d-none');
        orderDetailsView.classList.add('d-none');
    }

    // Function to load order details
    function loadOrderDetails(orderId) {
        console.log('Loading order details for ID:', orderId);
        
        // Show loading state
        orderDetailsContent.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Loading order details...</p>
            </div>
        `;
        
        // Hide orders list and show details view
        ordersListView.classList.add('d-none');
        orderDetailsView.classList.remove('d-none');
        
        // Fetch order details via AJAX
        fetch(`/profile/orders/${orderId}/details`)
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(html => {
                console.log('Received HTML response');
                orderDetailsContent.innerHTML = html;
                // Scroll to top after content loads
                window.scrollTo(0, 0);
            })
            .catch(error => {
                console.error('Error loading order details:', error);
                orderDetailsContent.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Error loading order details. Please try again.
                    </div>
                `;
            });
    }
    
    // Add click event listeners to view order details buttons
    viewOrderDetailsButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('View details button clicked');
            const orderId = this.getAttribute('data-order-id');
            console.log('Order ID:', orderId);
            loadOrderDetails(orderId);
        });
    });
    
    // Add click event listener to back to orders button
    if (backToOrdersButton) {
        backToOrdersButton.addEventListener('click', function(e) {
            e.preventDefault();
            showOrdersList();
        });
    }

    // Add click event listener to orders sidebar link
    if (ordersSidebarLink) {
        // Handle tab show event
        ordersSidebarLink.addEventListener('shown.bs.tab', function() {
            // If we're in order details view, switch back to orders list
            if (!orderDetailsView.classList.contains('d-none')) {
                showOrdersList();
            }
        });

        // Handle direct click event
        ordersSidebarLink.addEventListener('click', function(e) {
            // If we're in order details view, switch back to orders list
            if (!orderDetailsView.classList.contains('d-none')) {
                showOrdersList();
            }
        });
    }
});
</script>
@endpush

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

<style>
    .profile-container {
        min-height: calc(100vh - 150px);
        position: relative;
    }

    .profile-sidebar {
        position: sticky;
        top: 0;
        height: 100%;
        z-index: 1;
    }

    .profile-content {
        height: 100%;
    }

    .tab-content {
        height: 100%;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: rgba(0, 0, 0, 0.2) transparent;
    }
    
    .tab-content::-webkit-scrollbar {
        width: 6px;
    }
    
    .tab-content::-webkit-scrollbar-track {
        background: transparent;
    }
    
    .tab-content::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
        border-radius: 3px;
    }
    
    .tab-content:not(:hover)::-webkit-scrollbar-thumb {
        background-color: transparent;
    }

    .pagination {
        margin-bottom: 0;
    }

    .page-link {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        min-width: 32px;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background-color: #fff;
        border-color: #dee2e6;
    }

    @media (min-width: 992px) {
        .profile-container {
            display: flex;
        }
        
        .profile-sidebar {
            flex: 0 0 25%;
            max-width: 25%;
        }
        
        .profile-content {
            flex: 0 0 75%;
            max-width: 75%;
        }
    }

    .container.py-4 {
        padding-top: 1rem !important;
    }

    .card {
        margin-bottom: 1rem;
    }
</style> 