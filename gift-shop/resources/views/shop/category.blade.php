@extends('layouts.shop')

@section('title', $category->name)

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Shop</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Filters</h5>
                    <form action="{{ route('categories.show', $category->slug) }}" method="GET">
                        <!-- Price Range -->
                        <div class="mb-4">
                            <h6 class="mb-3">Price Range</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" class="form-control" name="price_min" 
                                           placeholder="Min" value="{{ request('price_min') }}">
                                </div>
                                <div class="col-6">
                                    <input type="number" class="form-control" name="price_max" 
                                           placeholder="Max" value="{{ request('price_max') }}">
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
                            <a href="{{ route('categories.show', $category->slug) }}" class="btn btn-outline-secondary">Clear Filters</a>
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
                    <h4 class="mb-1">{{ $category->name }}</h4>
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
                    <p class="mt-3 text-muted">No products found in this category.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-primary mt-2">Browse All Products</a>
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
                                    <div class="d-grid">
                                        <a href="{{ route('products.show', $product->slug) }}" class="btn btn-outline-primary">
                                            View Details
                                        </a>
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
        <form action="{{ route('categories.show', $category->slug) }}" method="GET">
            <!-- Price Range -->
            <div class="mb-4">
                <h6 class="mb-3">Price Range</h6>
                <div class="row g-2">
                    <div class="col-6">
                        <input type="number" class="form-control" name="price_min" 
                               placeholder="Min" value="{{ request('price_min') }}">
                    </div>
                    <div class="col-6">
                        <input type="number" class="form-control" name="price_max" 
                               placeholder="Max" value="{{ request('price_max') }}">
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
                <a href="{{ route('categories.show', $category->slug) }}" class="btn btn-outline-secondary">Clear Filters</a>
            </div>
        </form>
    </div>
</div>
@endsection 