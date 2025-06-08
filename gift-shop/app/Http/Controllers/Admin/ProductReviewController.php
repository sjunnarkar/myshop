<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    /**
     * Display a listing of reviews
     */
    public function index(Request $request)
    {
        $query = ProductReview::with(['product', 'user'])
            ->latest();

        // Filter by approval status
        if ($request->has('status')) {
            if ($request->status === 'pending') {
                $query->where('is_approved', false);
            } elseif ($request->status === 'approved') {
                $query->where('is_approved', true);
            }
        }

        // Filter by rating
        if ($request->has('rating') && $request->rating > 0) {
            $query->where('rating', $request->rating);
        }

        // Filter by verified purchase
        if ($request->has('verified')) {
            $query->where('verified_purchase', $request->verified === 'true');
        }

        $reviews = $query->paginate(15)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Show review details
     */
    public function show(ProductReview $review)
    {
        $review->load(['product', 'user']);
        return view('admin.reviews.show', compact('review'));
    }

    /**
     * Approve a review
     */
    public function approve(ProductReview $review)
    {
        $review->update(['is_approved' => true]);
        return back()->with('success', 'Review has been approved successfully.');
    }

    /**
     * Reject a review
     */
    public function reject(ProductReview $review)
    {
        $review->update(['is_approved' => false]);
        return back()->with('success', 'Review has been rejected successfully.');
    }

    /**
     * Delete a review
     */
    public function destroy(ProductReview $review)
    {
        $review->delete();
        return redirect()
            ->route('admin.reviews.index')
            ->with('success', 'Review has been deleted successfully.');
    }

    /**
     * Bulk approve reviews
     */
    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'reviews' => 'required|array',
            'reviews.*' => 'exists:product_reviews,id'
        ]);

        ProductReview::whereIn('id', $validated['reviews'])
            ->update(['is_approved' => true]);

        return back()->with('success', 'Selected reviews have been approved successfully.');
    }

    /**
     * Bulk reject reviews
     */
    public function bulkReject(Request $request)
    {
        $validated = $request->validate([
            'reviews' => 'required|array',
            'reviews.*' => 'exists:product_reviews,id'
        ]);

        ProductReview::whereIn('id', $validated['reviews'])
            ->update(['is_approved' => false]);

        return back()->with('success', 'Selected reviews have been rejected successfully.');
    }

    /**
     * Bulk delete reviews
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'reviews' => 'required|array',
            'reviews.*' => 'exists:product_reviews,id'
        ]);

        ProductReview::whereIn('id', $validated['reviews'])->delete();

        return back()->with('success', 'Selected reviews have been deleted successfully.');
    }
} 