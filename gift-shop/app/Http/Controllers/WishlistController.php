<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WishlistController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['show']);
    }

    /**
     * Display the user's wishlists.
     */
    public function index()
    {
        $wishlists = Auth::user()->wishlists()->withCount('items')->get();
        
        return redirect()->route('profile.show', ['#wishlist']);
    }

    /**
     * Show the form for creating a new wishlist.
     */
    public function create()
    {
        return view('wishlist.create');
    }

    /**
     * Store a newly created wishlist.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_public' => 'boolean',
        ]);

        $wishlist = Auth::user()->wishlists()->create($validated);

        return redirect()->route('profile.show', ['#wishlist'])
            ->with('success', 'Wishlist created successfully.');
    }

    /**
     * Display the specified wishlist.
     */
    public function show(Request $request, $id)
    {
        $wishlist = Wishlist::with(['items.product'])->findOrFail($id);
        
        // Check if this is a shared wishlist via token
        $sharedToken = $request->query('token');
        $isOwner = Auth::check() && Auth::id() === $wishlist->user_id;
        
        // Only allow viewing if public or owner or has valid token
        if (!$isOwner && !$wishlist->is_public && $sharedToken !== $wishlist->share_token) {
            abort(403, 'You do not have permission to view this wishlist.');
        }
        
        return view('wishlist.show', [
            'wishlist' => $wishlist,
            'isOwner' => $isOwner,
        ]);
    }

    /**
     * Show the form for editing the specified wishlist.
     */
    public function edit(Wishlist $wishlist)
    {
        $this->authorize('update', $wishlist);
        
        return view('wishlist.edit', compact('wishlist'));
    }

    /**
     * Update the specified wishlist.
     */
    public function update(Request $request, Wishlist $wishlist)
    {
        $this->authorize('update', $wishlist);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_public' => 'boolean',
        ]);
        
        $wishlist->update($validated);
        
        return redirect()->route('profile.show', ['#wishlist'])
            ->with('success', 'Wishlist updated successfully.');
    }

    /**
     * Remove the specified wishlist.
     */
    public function destroy(Wishlist $wishlist)
    {
        $this->authorize('delete', $wishlist);
        
        $wishlist->delete();
        
        return redirect()->route('profile.show', ['#wishlist'])
            ->with('success', 'Wishlist deleted successfully.');
    }

    /**
     * Add product to wishlist.
     */
    public function addProduct(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'wishlist_id' => 'nullable|exists:wishlists,id',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        $user = Auth::user();
        
        // If no wishlist specified, use default wishlist
        if (empty($validated['wishlist_id'])) {
            $wishlist = $user->defaultWishlist();
        } else {
            $wishlist = $user->wishlists()->findOrFail($validated['wishlist_id']);
        }
        
        // Check if product already in the wishlist
        if ($wishlist->hasProduct($validated['product_id'])) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product already in wishlist',
                ]);
            }
            
            return back()->with('info', 'This product is already in your wishlist.');
        }
        
        // Add product to wishlist
        $attributes = [
            'notes' => $validated['notes'] ?? null,
        ];
        
        $wishlistItem = $wishlist->addProduct($validated['product_id'], $attributes);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Product added to wishlist',
                'wishlist_id' => $wishlist->id,
            ]);
        }
        
        return redirect()->back()->with('success', 'Product added to wishlist successfully.');
    }

    /**
     * Remove product from wishlist.
     */
    public function removeProduct(Request $request, Wishlist $wishlist, $productId)
    {
        $this->authorize('update', $wishlist);
        
        // Check if product exists in this wishlist
        $wishlistItem = $wishlist->items()->where('product_id', $productId)->first();
        
        if (!$wishlistItem) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not in wishlist',
                ]);
            }
            
            return back()->with('error', 'Product not found in wishlist.');
        }
        
        // Remove product from wishlist
        $wishlistItem->delete();
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Product removed from wishlist',
            ]);
        }
        
        return redirect()->back()->with('success', 'Product removed from wishlist.');
    }

    /**
     * Add all wishlist items to cart.
     */
    public function addAllToCart(Wishlist $wishlist)
    {
        $this->authorize('view', $wishlist);
        
        $cartService = app(\App\Services\CartService::class);
        $addedCount = 0;
        
        foreach ($wishlist->items as $item) {
            if ($item->product->is_available) {
                $cartService->add($item->product->id, 1);
                $addedCount++;
            }
        }
        
        if ($addedCount === 0) {
            return back()->with('info', 'No products were added to your cart. They might be unavailable.');
        }
        
        return redirect()->route('cart.index')
            ->with('success', "{$addedCount} product(s) from your wishlist were added to cart.");
    }

    /**
     * Generate or regenerate a sharing token for a wishlist.
     */
    public function generateShareToken(Wishlist $wishlist)
    {
        $this->authorize('update', $wishlist);
        
        $wishlist->share_token = Str::random(64);
        $wishlist->is_public = true;
        $wishlist->save();
        
        $shareUrl = route('wishlist.show', ['wishlist' => $wishlist->id, 'token' => $wishlist->share_token]);
        
        return back()->with('success', 'Wishlist is now shareable with the following link: ' . $shareUrl);
    }
} 