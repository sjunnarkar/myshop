@extends('layouts.shop')

@section('title', $product->name)

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css">
<style>
    .swiper {
        width: 100%;
        margin-left: auto;
        margin-right: auto;
        user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
    }
    .swiper-main {
        height: 400px;
    }
    .swiper-thumbs {
        height: 100px;
        box-sizing: border-box;
        padding: 10px 0;
    }
    .swiper-thumbs .swiper-slide {
        width: 100px;
        height: 100px;
        opacity: 0.4;
        cursor: pointer;
    }
    .swiper-thumbs .swiper-slide-thumb-active {
        opacity: 1;
    }
    .swiper-slide img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        pointer-events: none;
    }
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
    .swiper-button-next,
    .swiper-button-prev {
        color: #000;
        background: rgba(255, 255, 255, 0.8);
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }
    .swiper-button-next:after,
    .swiper-button-prev:after {
        font-size: 20px;
    }
</style>
@endpush

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Shop</a></li>
            <li class="breadcrumb-item">
                <a href="{{ route('categories.show', $product->category->slug) }}">
                    {{ $product->category->name }}
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Product Images -->
        <div class="col-lg-6 mb-4 mb-lg-0">
            <!-- Main Swiper -->
            <div class="swiper swiper-main mb-3">
                <div class="swiper-wrapper">
                    @if($product->thumbnail)
                        <div class="swiper-slide">
                            <img src="{{ Storage::url($product->thumbnail) }}" 
                                alt="{{ $product->name }}">
                        </div>
                    @endif
                    @if($product->additional_images)
                        @foreach($product->additional_images as $image)
                            <div class="swiper-slide">
                                <img src="{{ Storage::url($image) }}" 
                                    alt="{{ $product->name }}">
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>

            <!-- Thumbs Swiper -->
            @if($product->additional_images)
                <div class="swiper swiper-thumbs">
                    <div class="swiper-wrapper">
                        @if($product->thumbnail)
                            <div class="swiper-slide">
                                <img src="{{ Storage::url($product->thumbnail) }}" 
                                    alt="{{ $product->name }}">
                            </div>
                        @endif
                        @foreach($product->additional_images as $image)
                            <div class="swiper-slide">
                                <img src="{{ Storage::url($image) }}" 
                                    alt="{{ $product->name }}">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Product Info -->
        <div class="col-lg-6">
            <h1 class="h2 mb-2">{{ $product->name }}</h1>
            <p class="text-muted mb-4">
                Category: 
                <a href="{{ route('categories.show', $product->category->slug) }}" class="text-decoration-none">
                    {{ $product->category->name }}
                </a>
            </p>

            <div class="mb-4">
                <h3 class="h4 text-primary">{{ \App\Helpers\CurrencyHelper::format($product->base_price) }}</h3>
                @if($product->stock > 0)
                    <span class="badge bg-success">In Stock ({{ $product->stock }})</span>
                @else
                    <span class="badge bg-danger">Out of Stock</span>
                @endif
            </div>

            <div class="mb-4">
                <h5>Description</h5>
                <p class="text-muted">{{ $product->description }}</p>
            </div>

            @if($product->dimensions)
                <div class="mb-4">
                    <h5>Dimensions</h5>
                    <div class="row g-2">
                        @if(isset($product->dimensions['width']))
                            <div class="col-auto">
                                <span class="badge bg-light text-dark">
                                    Width: {{ $product->dimensions['width'] }}cm
                                </span>
                            </div>
                        @endif
                        @if(isset($product->dimensions['height']))
                            <div class="col-auto">
                                <span class="badge bg-light text-dark">
                                    Height: {{ $product->dimensions['height'] }}cm
                                </span>
                            </div>
                        @endif
                        @if(isset($product->dimensions['length']))
                            <div class="col-auto">
                                <span class="badge bg-light text-dark">
                                    Length: {{ $product->dimensions['length'] }}cm
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <form action="{{ route('cart.add') }}" method="POST" id="addToCartForm">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                
                @foreach($product->customizationTemplates as $template)
                    <div class="mb-4">
                        <h5>{{ $template->name }}</h5>
                        <p class="text-muted small mb-3">{{ $template->description }}</p>
                        
                        <input type="hidden" name="customization_details[{{ $loop->index }}][template_id]" value="{{ $template->id }}">
                        
                        <div class="row g-3">
                            @foreach($template->fields as $field)
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $field['name'] }}</h6>
                                            
                                            @if($field['type'] === 'text')
                                                <div class="mb-3">
                                                    <label class="form-label">{{ $field['name'] }}</label>
                                                    <input type="text" 
                                                        class="form-control" 
                                                        name="customization_details[{{ $loop->parent->index }}][fields][{{ $field['name'] }}]" 
                                                        placeholder="Enter {{ strtolower($field['name']) }}"
                                                        @if(isset($field['required']) && $field['required']) required @endif>
                                                </div>
                                            @elseif($field['type'] === 'select')
                                                <div class="mb-3">
                                                    <label class="form-label">{{ $field['name'] }}</label>
                                                    <select class="form-select" 
                                                        name="customization_details[{{ $loop->parent->index }}][fields][{{ $field['name'] }}]"
                                                        @if(isset($field['required']) && $field['required']) required @endif>
                                                        <option value="">Select {{ $field['name'] }}</option>
                                                        @foreach($field['options'] as $option)
                                                            <option value="{{ $option }}">{{ $option }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @elseif($field['type'] === 'number')
                                                <div class="mb-3">
                                                    <label class="form-label">{{ $field['name'] }}</label>
                                                    <input type="number" 
                                                        class="form-control" 
                                                        name="customization_details[{{ $loop->parent->index }}][fields][{{ $field['name'] }}]" 
                                                        placeholder="Enter {{ strtolower($field['name']) }}"
                                                        @if(isset($field['min'])) min="{{ $field['min'] }}" @endif
                                                        @if(isset($field['max'])) max="{{ $field['max'] }}" @endif
                                                        @if(isset($field['required']) && $field['required']) required @endif>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                @if($product->customization_options)
                    <div class="mb-4">
                        <h5>Customization Options</h5>
                        <div class="row g-3">
                            @foreach($product->customization_options as $option)
                                <div class="col-md-6">
                                    <label class="form-label">{{ $option['name'] }}</label>
                                    <select class="form-select" name="options[{{ $option['name'] }}]">
                                        <option value="">Select {{ $option['name'] }}</option>
                                        @foreach(explode(',', is_array($option['values']) ? implode(',', $option['values']) : $option['values']) as $value)
                                            <option value="{{ trim($value) }}">{{ trim($value) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="mb-4">
                    <label for="quantity" class="form-label">Quantity</label>
                    <input type="number" 
                        class="form-control" 
                        id="quantity" 
                        name="quantity" 
                        value="1" 
                        min="1" 
                        max="{{ $product->stock }}" 
                        required>
                </div>

                <div class="mb-4">
                    <label for="special_instructions" class="form-label">Special Instructions (Optional)</label>
                    <textarea class="form-control" 
                        id="special_instructions" 
                        name="special_instructions" 
                        rows="3" 
                        placeholder="Add any special instructions for your order"></textarea>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" 
                        class="btn btn-primary btn-lg" 
                        {{ $product->stock == 0 ? 'disabled' : '' }}>
                        <i class="bi bi-cart-plus"></i> Add to Cart
                    </button>
                </div>
            </form>
            <x-add-to-wishlist-button :product="$product" />
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
        <div class="mt-5">
            <h3 class="mb-4">Related Products</h3>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                @foreach($relatedProducts as $relatedProduct)
                    <div class="col">
                        <div class="card h-100 product-card">
                            @if($relatedProduct->thumbnail)
                                <img src="{{ Storage::url($relatedProduct->thumbnail) }}" 
                                    class="card-img-top" 
                                    alt="{{ $relatedProduct->name }}">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                    style="height: 200px;">
                                    <i class="bi bi-image text-muted fs-1"></i>
                                </div>
                            @endif
                            <div class="card-body">
                                <h6 class="card-title mb-1">{{ $relatedProduct->name }}</h6>
                                <p class="text-muted small mb-2">{{ $relatedProduct->category->name }}</p>
                                <p class="card-text text-primary fw-bold mb-0">{{ \App\Helpers\CurrencyHelper::format($relatedProduct->base_price) }}</p>
                            </div>
                            <div class="card-footer bg-white border-top-0">
                                <div class="d-grid">
                                    <a href="{{ route('products.show', $relatedProduct) }}" class="btn btn-outline-primary">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Product Reviews -->
    <div class="card mt-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4>Customer Reviews</h4>
                    <div class="d-flex align-items-center">
                        <div class="me-2">
                            <span class="h5 mb-0">{{ number_format($product->average_rating, 1) }}</span>
                            <span class="text-muted">out of 5</span>
                        </div>
                        <div class="me-3">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= round($product->average_rating))
                                    <i class="bi bi-star-fill text-warning"></i>
                                @else
                                    <i class="bi bi-star text-warning"></i>
                                @endif
                            @endfor
                        </div>
                        <div class="text-muted">
                            {{ $product->total_reviews }} {{ Str::plural('review', $product->total_reviews) }}
                        </div>
                    </div>
                </div>
                <div>
                    @auth
                        @if (!$product->reviews()->where('user_id', auth()->id())->exists())
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reviewModal">
                                Write a Review
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary">
                            Login to Write a Review
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Rating Distribution -->
            <div class="mb-4">
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
            </div>

            <!-- Reviews List -->
            <div class="reviews-list">
                @forelse($product->reviews()->approved()->with('user')->latest()->take(5)->get() as $review)
                    <div class="border-bottom pb-3 mb-3">
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
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <p>No reviews yet. Be the first to review this product!</p>
                    </div>
                @endforelse
            </div>

            @if($product->total_reviews > 5)
                <div class="text-center mt-3">
                    <a href="{{ route('products.reviews.index', $product) }}" class="btn btn-outline-primary">
                        View All Reviews
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Review Modal -->
    @auth
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
    @endauth
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize thumbs swiper if it exists
        let swiperThumbs = null;
        if (document.querySelector('.swiper-thumbs')) {
            swiperThumbs = new Swiper('.swiper-thumbs', {
                spaceBetween: 10,
                slidesPerView: 'auto',
                freeMode: true,
                watchSlidesProgress: true,
            });
        }

        // Initialize main swiper
        let swiperMain = new Swiper('.swiper-main', {
            spaceBetween: 10,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            thumbs: swiperThumbs ? {
                swiper: swiperThumbs,
            } : undefined,
        });
    });

    // Form validation
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('addToCartForm');
        
        form.addEventListener('submit', function(e) {
            // Get all form data
            const formData = new FormData(form);
            const data = {};
            for (let [key, value] of formData.entries()) {
                data[key] = value;
            }
            
            // Log the form data before submission
            console.log('Form Data:', data);
            
            // Validate required fields
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields');
                return;
            }
            
            // If all validations pass, allow the form to submit normally
        });
    });
</script>
@endpush 