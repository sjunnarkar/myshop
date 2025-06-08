<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Wishlist extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'is_public',
        'share_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($wishlist) {
            // Generate unique share token when creating a wishlist
            if ($wishlist->is_public && !$wishlist->share_token) {
                $wishlist->share_token = Str::random(64);
            }
        });

        static::updating(function ($wishlist) {
            // Generate or clear share token based on public status
            if ($wishlist->is_public && !$wishlist->share_token) {
                $wishlist->share_token = Str::random(64);
            } elseif (!$wishlist->is_public) {
                $wishlist->share_token = null;
            }
        });
    }

    /**
     * Get the user that owns the wishlist.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items in the wishlist.
     */
    public function items(): HasMany
    {
        return $this->hasMany(WishlistItem::class);
    }

    /**
     * Get the products in the wishlist.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'wishlist_items')
            ->withPivot('notes', 'priority')
            ->withTimestamps();
    }

    /**
     * Check if the wishlist contains a specific product.
     *
     * @param int $productId
     * @return bool
     */
    public function hasProduct(int $productId): bool
    {
        return $this->items()->where('product_id', $productId)->exists();
    }

    /**
     * Add a product to the wishlist.
     *
     * @param int $productId
     * @param array $attributes
     * @return WishlistItem|null
     */
    public function addProduct(int $productId, array $attributes = []): ?WishlistItem
    {
        if (!$this->hasProduct($productId)) {
            return $this->items()->create(array_merge(['product_id' => $productId], $attributes));
        }
        
        return null;
    }

    /**
     * Remove a product from the wishlist.
     *
     * @param int $productId
     * @return bool
     */
    public function removeProduct(int $productId): bool
    {
        return (bool) $this->items()->where('product_id', $productId)->delete();
    }
} 