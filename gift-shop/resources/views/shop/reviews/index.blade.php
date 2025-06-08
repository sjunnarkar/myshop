@extends('layouts.shop')

@section('title', 'Reviews - ' . $product->name)

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('shop.index') }}">Shop</a></li>
            <li class="breadcrumb-item">
                <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}">
                    {{ $product->category->name }}
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('shop.show', $product) }}">{{ $product->name }}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Reviews</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Product Summary -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex mb-3">
                        @if($product->thumbnail)
                            <img src="{{ Storage::url($product->thumbnail) }}" 
                                 alt="{{ $product->name }}"
                                 class="img-thumbnail me-3"
                                 style="width: 100px; height: 100px; object-fit: cover;">
                        @endif
                        <div>
                            <h5 class="mb-1">{{ $product->name }}</h5>
                            <p class="text-muted mb-0">{{ $product->category->name }}</p>
                        </div>
                    </div>

                    <div class="text-center border-top pt-3">
                        <div class="h2 mb-0">{{ number_format($product->average_rating, 1) }}</div>
                        <div class="text-warning mb-2">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= round($product->average_rating))
                                    <i class="bi bi-star-fill"></i>
                                @else
                                    <i class="bi bi-star"></i>
                                @endif
                            @endfor
                        </div>
                        <div class="text-muted">
                            Based on {{ $product->total_reviews }} {{ Str::plural('review', $product->total_reviews) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rating Distribution -->
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="mb-3">Rating Distribution</h6>
                    @foreach($product->rating_distribution as $rating => $data)
                        <div class="d-flex align-items-center mb-2">
                            <div class="text-nowrap me-3" style="width: 60px;">
                                {{ $rating }} {{ Str::plural('star', $rating) }}
                            </div>
                            <div class="progress flex-grow-1" style="height: 8px;">
                                <div class="progress-bar bg-warning" 
                                     role="progressbar" 
                                     style="width: {{ $data['percentage'] }}%" 
                                     aria-valuenow="{{ $data['percentage'] }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                            <div class="text-muted ms-3" style="width: 50px;">
                                {{ $data['count'] }}
                            </div>
                        </div>
                    @endforeach

                    <div class="text-center mt-3">
                        <div class="text-muted">
                            {{ number_format($product->verified_reviews_percentage, 0) }}% verified purchases
                        </div>
                    </div>
                </div>
            </div>

            @auth
                @if (!$product->reviews()->where('user_id', auth()->id())->exists())
                    <div class="d-grid">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reviewModal">
                            Write a Review
                        </button>
                    </div>
                @endif
            @else
                <div class="d-grid">
                    <a href="{{ route('login') }}" class="btn btn-outline-primary">
                        Login to Write a Review
                    </a>
                </div>
            @endauth
        </div>

        <!-- Reviews List -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    @forelse($reviews as $review)
                        <div class="border-bottom pb-4 mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <div>
                                    <strong>{{ $review->user->name }}</strong>
                                    @if($review->verified_purchase)
                                        <span class="badge bg-success ms-2">Verified Purchase</span>
                                    @endif
                                </div>
                                <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                            </div>
                            <div class="mb-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $review->rating)
                                        <i class="bi bi-star-fill text-warning"></i>
                                    @else
                                        <i class="bi bi-star text-warning"></i>
                                    @endif
                                @endfor
                            </div>
                            <p class="mb-2">{{ $review->review }}</p>
                            @if($review->pros)
                                <div class="text-success mb-1">
                                    <small><strong>Pros:</strong> {{ $review->pros }}</small>
                                </div>
                            @endif
                            @if($review->cons)
                                <div class="text-danger mb-1">
                                    <small><strong>Cons:</strong> {{ $review->cons }}</small>
                                </div>
                            @endif

                            @if(auth()->id() === $review->user_id)
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editReviewModal{{ $review->id }}">
                                        Edit
                                    </button>
                                    <form action="{{ route('products.reviews.destroy', [$product, $review]) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this review?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </div>

                                <!-- Edit Review Modal -->
                                <div class="modal fade" id="editReviewModal{{ $review->id }}" 
                                     tabindex="-1" 
                                     aria-labelledby="editReviewModalLabel{{ $review->id }}" 
                                     aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('products.reviews.update', [$product, $review]) }}" 
                                                  method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editReviewModalLabel{{ $review->id }}">
                                                        Edit Review
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Rating</label>
                                                        <div class="rating">
                                                            @for ($i = 5; $i >= 1; $i--)
                                                                <input type="radio" 
                                                                       name="rating" 
                                                                       value="{{ $i }}" 
                                                                       id="rating{{ $review->id }}{{ $i }}"
                                                                       {{ $review->rating === $i ? 'checked' : '' }}
                                                                       required>
                                                                <label for="rating{{ $review->id }}{{ $i }}">
                                                                    <i class="bi bi-star-fill"></i>
                                                                </label>
                                                            @endfor
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="review{{ $review->id }}" class="form-label">Review</label>
                                                        <textarea class="form-control" 
                                                                  id="review{{ $review->id }}" 
                                                                  name="review" 
                                                                  rows="3" 
                                                                  required 
                                                                  minlength="10">{{ $review->review }}</textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="pros{{ $review->id }}" class="form-label">
                                                            Pros (Optional)
                                                        </label>
                                                        <input type="text" 
                                                               class="form-control" 
                                                               id="pros{{ $review->id }}" 
                                                               name="pros" 
                                                               value="{{ $review->pros }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="cons{{ $review->id }}" class="form-label">
                                                            Cons (Optional)
                                                        </label>
                                                        <input type="text" 
                                                               class="form-control" 
                                                               id="cons{{ $review->id }}" 
                                                               name="cons" 
                                                               value="{{ $review->cons }}">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        Cancel
                                                    </button>
                                                    <button type="submit" class="btn btn-primary">
                                                        Update Review
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <p class="mb-0">No reviews yet. Be the first to review this product!</p>
                        </div>
                    @endforelse

                    <div class="mt-4">
                        {{ $reviews->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Write Review Modal -->
@auth
    @if (!$product->reviews()->where('user_id', auth()->id())->exists())
        <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('products.reviews.store', $product) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="reviewModalLabel">Write a Review</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Rating</label>
                                <div class="rating">
                                    @for ($i = 5; $i >= 1; $i--)
                                        <input type="radio" name="rating" value="{{ $i }}" id="rating{{ $i }}" required>
                                        <label for="rating{{ $i }}">
                                            <i class="bi bi-star-fill"></i>
                                        </label>
                                    @endfor
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="review" class="form-label">Review</label>
                                <textarea class="form-control" id="review" name="review" rows="3" required 
                                          minlength="10" placeholder="Share your experience with this product"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="pros" class="form-label">Pros (Optional)</label>
                                <input type="text" class="form-control" id="pros" name="pros" 
                                       placeholder="What did you like about this product?">
                            </div>
                            <div class="mb-3">
                                <label for="cons" class="form-label">Cons (Optional)</label>
                                <input type="text" class="form-control" id="cons" name="cons" 
                                       placeholder="What could be improved?">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Submit Review</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endauth

@push('styles')
<style>
    .rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
    }
    .rating input {
        display: none;
    }
    .rating label {
        cursor: pointer;
        padding: 5px;
        color: #ddd;
    }
    .rating label:hover,
    .rating label:hover ~ label,
    .rating input:checked ~ label {
        color: #ffc107;
    }
</style>
@endpush 