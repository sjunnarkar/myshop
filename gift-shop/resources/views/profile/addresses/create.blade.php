@extends('layouts.shop')

@section('title', 'Add New Address')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Add New Address</h5>
                        <a href="{{ route('profile.show') }}#addresses" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left me-1"></i> Back to Addresses
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.addresses.store') }}" method="POST">
                        @csrf

                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Address Name</label>
                            <input type="text" 
                                class="form-control @error('name') is-invalid @enderror" 
                                id="name" 
                                name="name" 
                                value="{{ old('name') }}" 
                                placeholder="e.g. Home, Office, etc."
                                required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" 
                                class="form-control @error('phone') is-invalid @enderror" 
                                id="phone" 
                                name="phone" 
                                value="{{ old('phone') }}"
                                required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="mb-3">
                            <label for="address" class="form-label">Street Address</label>
                            <input type="text" 
                                class="form-control @error('address') is-invalid @enderror" 
                                id="address" 
                                name="address" 
                                value="{{ old('address') }}"
                                required>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- City -->
                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label">City</label>
                                <input type="text" 
                                    class="form-control @error('city') is-invalid @enderror" 
                                    id="city" 
                                    name="city" 
                                    value="{{ old('city') }}"
                                    required>
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- State -->
                            <div class="col-md-6 mb-3">
                                <label for="state" class="form-label">State</label>
                                <input type="text" 
                                    class="form-control @error('state') is-invalid @enderror" 
                                    id="state" 
                                    name="state" 
                                    value="{{ old('state') }}"
                                    required>
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- ZIP -->
                            <div class="col-md-6 mb-3">
                                <label for="zip" class="form-label">ZIP Code</label>
                                <input type="text" 
                                    class="form-control @error('zip') is-invalid @enderror" 
                                    id="zip" 
                                    name="zip" 
                                    value="{{ old('zip') }}"
                                    required>
                                @error('zip')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Country -->
                            <div class="col-md-6 mb-3">
                                <label for="country" class="form-label">Country</label>
                                <input type="text" 
                                    class="form-control @error('country') is-invalid @enderror" 
                                    id="country" 
                                    name="country" 
                                    value="{{ old('country') }}"
                                    required>
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Address Type -->
                        <div class="mb-4">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" 
                                    type="checkbox" 
                                    id="is_shipping" 
                                    name="is_shipping" 
                                    value="1" 
                                    {{ old('is_shipping') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_shipping">
                                    Use as shipping address
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" 
                                    type="checkbox" 
                                    id="is_billing" 
                                    name="is_billing" 
                                    value="1" 
                                    {{ old('is_billing') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_billing">
                                    Use as billing address
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" 
                                    type="checkbox" 
                                    id="is_default" 
                                    name="is_default" 
                                    value="1" 
                                    {{ old('is_default') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_default">
                                    Set as default address
                                </label>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                Add Address
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 