<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'city',
        'state',
        'zip',
        'country',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',
    ];

    /**
     * Hash the password when setting it.
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Get all addresses for the user.
     */
    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    /**
     * Get the default shipping address.
     */
    public function defaultShippingAddress()
    {
        return $this->hasOne(UserAddress::class)->where('is_default', true)->where('is_shipping', true);
    }

    /**
     * Get the default billing address.
     */
    public function defaultBillingAddress()
    {
        return $this->hasOne(UserAddress::class)->where('is_default', true)->where('is_billing', true);
    }

    /**
     * Get the user's orders.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the user's wishlists.
     */
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Get the default wishlist for the user.
     * Creates one if it doesn't exist.
     */
    public function defaultWishlist()
    {
        $defaultWishlist = $this->wishlists()->firstOrCreate(
            ['name' => 'My Wishlist'],
            ['description' => 'Default wishlist']
        );
        
        return $defaultWishlist;
    }

    /**
     * Check if a product is in any of the user's wishlists.
     */
    public function hasProductInWishlist(int $productId): bool
    {
        return $this->wishlists()
            ->whereHas('items', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->exists();
    }

    /**
     * Get the wishlist containing a specific product.
     */
    public function getWishlistContainingProduct(int $productId): ?Wishlist
    {
        return $this->wishlists()
            ->whereHas('items', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->first();
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * Get the user's full address.
     */
    public function getFullAddressAttribute(): string
    {
        return implode(', ', array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->zip,
            $this->country
        ]));
    }

    /**
     * Get the user's cart items.
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
}
