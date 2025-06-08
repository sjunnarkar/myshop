<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WishlistItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'wishlist_id',
        'product_id',
        'notes',
        'priority',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'priority' => 'integer',
    ];

    /**
     * Get the wishlist that owns the item.
     */
    public function wishlist(): BelongsTo
    {
        return $this->belongsTo(Wishlist::class);
    }

    /**
     * Get the product for this wishlist item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
} 