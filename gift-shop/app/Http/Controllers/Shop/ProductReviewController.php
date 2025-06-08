<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Store a new review
     */
    public function store(Request $request, Product $product)
    {
        // Validate request
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|min:10',
            'pros' => 'nullable|string',
            'cons' => 'nullable|string',
        ]);

        // Check if user has already reviewed this product
        if ($product->reviews()->where('user_id', auth()->id())->exists()) {
            return back()->with('error', 'You have already reviewed this product.');
        }

        // Check if user has purchased the product
        $hasOrdered = $product->orderItems()
            ->whereHas('order', function($query) {
                $query->where('user_id', auth()->id())
                    ->where('status', 'delivered');
            })
            ->exists();

        // Create the review
        $review = $product->reviews()->create([
            'user_id' => auth()->id(),
            'rating' => $validated['rating'],
            'review' => $validated['review'],
            'pros' => $validated['pros'],
            'cons' => $validated['cons'],
            'verified_purchase' => $hasOrdered,
            'is_approved' => !config('shop.review_approval_required', true)
        ]);

        return back()->with('success', 
            config('shop.review_approval_required', true)
                ? 'Thank you! Your review has been submitted and is pending approval.'
                : 'Thank you! Your review has been posted successfully.'
        );
    }

    /**
     * Update an existing review
     */
    public function update(Request $request, Product $product, ProductReview $review)
    {
        // Ensure the review belongs to the authenticated user
        if ($review->user_id !== auth()->id()) {
            abort(403);
        }

        // Validate request
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|min:10',
            'pros' => 'nullable|string',
            'cons' => 'nullable|string',
        ]);

        // Update the review
        $review->update([
            'rating' => $validated['rating'],
            'review' => $validated['review'],
            'pros' => $validated['pros'],
            'cons' => $validated['cons'],
            'is_approved' => !config('shop.review_approval_required', true)
        ]);

        return back()->with('success', 
            config('shop.review_approval_required', true)
                ? 'Your review has been updated and is pending approval.'
                : 'Your review has been updated successfully.'
        );
    }

    /**
     * Delete a review
     */
    public function destroy(Product $product, ProductReview $review)
    {
        // Ensure the review belongs to the authenticated user
        if ($review->user_id !== auth()->id()) {
            abort(403);
        }

        $review->delete();

        return back()->with('success', 'Your review has been deleted successfully.');
    }

    /**
     * List reviews for a product
     */
    public function index(Product $product)
    {
        $reviews = $product->reviews()
            ->with('user')
            ->approved()
            ->latest()
            ->paginate(10);

        return view('shop.reviews.index', compact('product', 'reviews'));
    }
} 