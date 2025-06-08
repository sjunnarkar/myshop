@extends('layouts.shop')

@section('title', request('category') ? ucfirst(request('category')) : 'Shop')

@section('content')
<div class="container">
    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Filters</h5>
                    <form action="{{ route('products.index') }}" method="GET">
                        <!-- Categories -->
                        <div class="mb-4">
                            <h6 class="mb-3">Categories</h6>
                            @foreach($categories as $category)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" 
                                        type="radio" 
                                        name="category" 
                                        id="category_{{ $category->id }}" 
                                        value="{{ $category->slug }}"
                                        {{ request('category') == $category->slug ? 'checked' : '' }}>
                                    <label class="form-check-label" for="category_{{ $category->id }}">
                                        {{ $category->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <!-- Price Range -->
                        <div class="mb-4">
                            <h6 class="mb-3">Price Range</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="input-group">
                                        <span class="input-group-text">{{ config('app.currency_symbol') }}</span>
                                        <input type="number" 
                                            class="form-control" 
                                            name="price_min" 
                                            value="{{ request('price_min') }}" 
                                            min="{{ floor($priceRange->min_price) }}" 
                                            max="{{ ceil($priceRange->max_price) }}"
                                            placeholder="Min">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="input-group">
                                        <span class="input-group-text">{{ config('app.currency_symbol') }}</span>
                                        <input type="number" 
                                            class="form-control" 
                                            name="price_max" 
                                            value="{{ request('price_max') }}" 
                                            min="{{ floor($priceRange->min_price) }}" 
                                            max="{{ ceil($priceRange->max_price) }}"
                                            placeholder="Max">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sort -->
                        <div class="mb-4">
                            <h6 class="mb-3">Sort By</h6>
                            <select class="form-select" name="sort">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Clear Filters</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9">
            <!-- Results Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">{{ request('category') ? ucfirst(request('category')) : 'All Products' }}</h4>
                    <p class="text-muted mb-0">{{ $products->total() }} products found</p>
                </div>
                <div class="d-lg-none">
                    <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#filtersOffcanvas">
                        <i class="bi bi-funnel"></i> Filters
                    </button>
                </div>
            </div>

            @if($products->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-box fs-1 text-muted"></i>
                    <p class="mt-3 text-muted">No products found matching your criteria.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-primary mt-2">Clear Filters</a>
                </div>
            @else
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    @foreach($products as $product)
                        <div class="col">
                            <div class="card h-100 product-card">
                                @if($product->thumbnail)
                                    <img src="{{ Storage::url($product->thumbnail) }}" 
                                        class="card-img-top" 
                                        alt="{{ $product->name }}">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" 
                                        style="height: 200px;">
                                        <i class="bi bi-image text-muted fs-1"></i>
                                    </div>
                                @endif
                                <div class="card-body">
                                    <h6 class="card-title mb-1">{{ $product->name }}</h6>
                                    <p class="text-muted small mb-2">{{ $product->category->name }}</p>
                                    <p class="card-text text-primary fw-bold">{{ config('app.currency_symbol') }}{{ number_format($product->base_price, 2) }}</p>
                                </div>
                                <div class="card-footer bg-white border-top-0">
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('products.show', $product->slug) }}" class="btn btn-outline-primary flex-grow-1">
                                            <i class="bi bi-eye me-1"></i>
                                            <span>View</span>
                                        </a>
                                        <form action="{{ route('cart.add') }}" method="POST" class="flex-grow-1">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="bi bi-cart-plus me-1"></i>
                                                <span>Add</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Mobile Filters Offcanvas -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="filtersOffcanvas">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Filters</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('products.index') }}" method="GET">
            <!-- Categories -->
            <div class="mb-4">
                <h6 class="mb-3">Categories</h6>
                @foreach($categories as $category)
                    <div class="form-check mb-2">
                        <input class="form-check-input" 
                            type="radio" 
                            name="category" 
                            id="category_mobile_{{ $category->id }}" 
                            value="{{ $category->slug }}"
                            {{ request('category') == $category->slug ? 'checked' : '' }}>
                        <label class="form-check-label" for="category_mobile_{{ $category->id }}">
                            {{ $category->name }}
                        </label>
                    </div>
                @endforeach
            </div>

            <!-- Price Range -->
            <div class="mb-4">
                <h6 class="mb-3">Price Range</h6>
                <div class="row g-2">
                    <div class="col-6">
                        <div class="input-group">
                            <span class="input-group-text">{{ config('app.currency_symbol') }}</span>
                            <input type="number" 
                                class="form-control" 
                                name="price_min" 
                                value="{{ request('price_min') }}" 
                                min="{{ floor($priceRange->min_price) }}" 
                                max="{{ ceil($priceRange->max_price) }}"
                                placeholder="Min">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="input-group">
                            <span class="input-group-text">{{ config('app.currency_symbol') }}</span>
                            <input type="number" 
                                class="form-control" 
                                name="price_max" 
                                value="{{ request('price_max') }}" 
                                min="{{ floor($priceRange->min_price) }}" 
                                max="{{ ceil($priceRange->max_price) }}"
                                placeholder="Max">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sort -->
            <div class="mb-4">
                <h6 class="mb-3">Sort By</h6>
                <select class="form-select" name="sort">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                </select>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Clear Filters</a>
            </div>
        </form>
    </div>
</div>
@endsection 