<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Auth\Access\HandlesAuthorization;

class WishlistPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the wishlist.
     */
    public function view(User $user, Wishlist $wishlist): bool
    {
        // Owner can always view their wishlists
        if ($user->id === $wishlist->user_id) {
            return true;
        }

        // Public wishlists can be viewed by anyone
        return $wishlist->is_public;
    }

    /**
     * Determine whether the user can update the wishlist.
     */
    public function update(User $user, Wishlist $wishlist): bool
    {
        // Only the owner can update the wishlist
        return $user->id === $wishlist->user_id;
    }

    /**
     * Determine whether the user can delete the wishlist.
     */
    public function delete(User $user, Wishlist $wishlist): bool
    {
        // Only the owner can delete the wishlist
        return $user->id === $wishlist->user_id;
    }
} 