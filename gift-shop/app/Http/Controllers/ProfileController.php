<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function show()
    {
        $user = Auth::user();
        $addresses = $user->addresses;
        $orders = $user->orders()->latest()->paginate(10);
        $wishlists = $user->wishlists()->withCount('items')->get();
        $defaultWishlistItems = $user->defaultWishlist()->items()->with('product')->paginate(12);

        return view('profile.show', compact('user', 'addresses', 'orders', 'wishlists', 'defaultWishlistItems'));
    }

    /**
     * Update the user's profile.
     */
    public function update(ProfileUpdateRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        // Update user profile
        $user->update($data);

        return redirect()->route('profile.show')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();
        $user->update([
            'password' => $request->password,
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

    /**
     * Show the form for adding a new address.
     */
    public function createAddress()
    {
        return view('profile.addresses.create');
    }

    /**
     * Store a new address.
     */
    public function storeAddress(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address_type' => 'required|string|in:home,office,other',
            'street_address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'is_shipping' => 'boolean',
            'is_billing' => 'boolean'
        ]);

        $address = Auth::user()->addresses()->create([
            'name' => $data['name'],
            'address_type' => $data['address_type'],
            'street_address' => $data['street_address'],
            'city' => $data['city'],
            'state' => $data['state'],
            'postal_code' => $data['postal_code'],
            'country' => $data['country'],
            'phone' => $data['phone'],
            'is_shipping' => $request->has('is_shipping'),
            'is_billing' => $request->has('is_billing')
        ]);

        // If set as default shipping address
        if ($request->has('is_shipping')) {
            Auth::user()->addresses()
                ->where('id', '!=', $address->id)
                ->update(['is_shipping' => false]);
        }

        // If set as default billing address
        if ($request->has('is_billing')) {
            Auth::user()->addresses()
                ->where('id', '!=', $address->id)
                ->update(['is_billing' => false]);
        }

        return redirect()->route('profile.show', ['#addresses'])
            ->with('success', 'Address added successfully.');
    }

    /**
     * Show the form for editing an address.
     */
    public function editAddress(UserAddress $address)
    {
        $this->authorize('update', $address);
        return view('profile.addresses.edit', compact('address'));
    }

    /**
     * Update an address.
     */
    public function updateAddress(Request $request, UserAddress $address)
    {
        $this->authorize('update', $address);

        // If the request only contains shipping or billing flags
        if ($request->has('is_shipping') || $request->has('is_billing')) {
            $data = [];
            
            // Handle shipping flag
            if ($request->has('is_shipping')) {
                $data['is_shipping'] = true;
                // Only unset other addresses if this one is being set as shipping
                Auth::user()->addresses()
                    ->where('id', '!=', $address->id)
                    ->update(['is_shipping' => false]);
            } else if ($request->missing('is_shipping') && $request->hasAny(['is_shipping', 'is_billing'])) {
                $data['is_shipping'] = false;
            }

            // Handle billing flag
            if ($request->has('is_billing')) {
                $data['is_billing'] = true;
                // Only unset other addresses if this one is being set as billing
                Auth::user()->addresses()
                    ->where('id', '!=', $address->id)
                    ->update(['is_billing' => false]);
            } else if ($request->missing('is_billing') && $request->hasAny(['is_shipping', 'is_billing'])) {
                $data['is_billing'] = false;
            }

            $address->update($data);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Address preferences updated successfully',
                    'address' => $address->fresh()
                ]);
            }

            return redirect()->route('profile.show', ['#addresses'])
                ->with('success', 'Address preferences updated successfully.');
        }

        // Handle full address update
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address_type' => 'required|string|in:home,office,other',
            'street_address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $address->update($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Address updated successfully',
                'address' => $address->fresh()
            ]);
        }

        // Check if the request came from the checkout page
        if ($request->has('redirect_checkout')) {
            return redirect()->route('checkout.index')
                ->with('success', 'Address updated successfully.');
        }

        return redirect()->route('profile.show', ['#addresses'])
            ->with('success', 'Address updated successfully.');
    }

    /**
     * Delete an address.
     */
    public function destroyAddress(UserAddress $address)
    {
        $this->authorize('delete', $address);
        $address->delete();

        return redirect()->route('profile.show')
            ->with('success', 'Address deleted successfully.');
    }

    /**
     * Add a product to the user's wishlist.
     */
    public function addToWishlist(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $wishlist = Auth::user()->defaultWishlist();
        $wishlist->items()->syncWithoutDetaching([$request->product_id]);

        return back()->with('success', 'Product added to wishlist.');
    }

    /**
     * Remove a product from the user's wishlist.
     */
    public function removeFromWishlist(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $wishlist = Auth::user()->defaultWishlist();
        $wishlist->items()->detach($request->product_id);

        return back()->with('success', 'Product removed from wishlist.');
    }

    /**
     * Update address preferences (shipping/billing).
     */
    public function updateAddressPreferences(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:user_addresses,id',
            'is_shipping' => 'boolean',
            'is_billing' => 'boolean'
        ]);

        $address = Auth::user()->addresses()->findOrFail($request->address_id);
        $this->authorize('update', $address);

        if ($request->has('is_shipping')) {
            // Reset all other addresses' shipping flag
            Auth::user()->addresses()
                ->where('id', '!=', $address->id)
                ->update(['is_shipping' => false]);
            
            $address->update(['is_shipping' => true]);
        }

        if ($request->has('is_billing')) {
            // Reset all other addresses' billing flag
            Auth::user()->addresses()
                ->where('id', '!=', $address->id)
                ->update(['is_billing' => false]);
            
            $address->update(['is_billing' => true]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Address preferences updated successfully',
                'address' => $address->fresh()
            ]);
        }

        return redirect()->back()
            ->with('success', 'Address preferences updated successfully.');
    }
} 