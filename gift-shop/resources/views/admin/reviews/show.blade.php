@extends('layouts.admin')

@section('title', 'Review Details')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Review Details</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.reviews.index') }}">Reviews</a></li>
        <li class="breadcrumb-item active">Review Details</li>
    </ol>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <!-- Review Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>Review Content</div>
                        <div>
                            @if(!$review->is_approved)
                                <form action="{{ route('admin.reviews.approve', $review) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fas fa-check me-1"></i> Approve
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.reviews.reject', $review) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-warning btn-sm">
                                        <i class="fas fa-times me-1"></i> Reject
                                    </button>
                                </form>
                            @endif
                            <form action="{{ route('admin.reviews.destroy', $review) }}" 
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Are you sure you want to delete this review?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash me-1"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <div class="text-warning me-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        <i class="fas fa-star"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <div>
                                <span class="badge bg-{{ $review->is_approved ? 'success' : 'warning text-dark' }}">
                                    {{ $review->is_approved ? 'Approved' : 'Pending' }}
                                </span>
                                @if($review->verified_purchase)
                                    <span class="badge bg-info ms-1">Verified Purchase</span>
                                @endif
                            </div>
                        </div>
                        <p class="mb-3">{{ $review->review }}</p>
                        @if($review->pros)
                            <div class="mb-2">
                                <strong class="text-success">Pros:</strong>
                                <p class="mb-0">{{ $review->pros }}</p>
                            </div>
                        @endif
                        @if($review->cons)
                            <div>
                                <strong class="text-danger">Cons:</strong>
                                <p class="mb-0">{{ $review->cons }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Product Info -->
            <div class="card mb-4">
                <div class="card-header">Product Information</div>
                <div class="card-body">
                    <div class="d-flex mb-3">
                        @if($review->product->thumbnail)
                            <img src="{{ Storage::url($review->product->thumbnail) }}" 
                                 alt="{{ $review->product->name }}"
                                 class="img-thumbnail me-3"
                                 style="width: 80px; height: 80px; object-fit: cover;">
                        @endif
                        <div>
                            <h6 class="mb-1">
                                <a href="{{ route('shop.show', $review->product) }}" target="_blank">
                                    {{ $review->product->name }}
                                </a>
                            </h6>
                            <p class="text-muted mb-0">
                                Category: {{ $review->product->category->name }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="card mb-4">
                <div class="card-header">Customer Information</div>
                <div class="card-body">
                    <p class="mb-1"><strong>Name:</strong> {{ $review->user->name }}</p>
                    <p class="mb-1"><strong>Email:</strong> {{ $review->user->email }}</p>
                    <p class="mb-1">
                        <strong>Joined:</strong> 
                        {{ $review->user->created_at->format('M d, Y') }}
                    </p>
                    <p class="mb-0">
                        <strong>Total Reviews:</strong> 
                        {{ $review->user->reviews()->count() }}
                    </p>
                </div>
            </div>

            <!-- Review Meta -->
            <div class="card">
                <div class="card-header">Review Details</div>
                <div class="card-body">
                    <p class="mb-1">
                        <strong>Submitted:</strong> 
                        {{ $review->created_at->format('M d, Y H:i:s') }}
                    </p>
                    <p class="mb-1">
                        <strong>Last Updated:</strong> 
                        {{ $review->updated_at->format('M d, Y H:i:s') }}
                    </p>
                    <p class="mb-0">
                        <strong>IP Address:</strong> 
                        <span class="text-muted">Not tracked</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 