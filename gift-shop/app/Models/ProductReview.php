<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductReview extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'user_id',
        'rating',
        'review',
        'pros',
        'cons',
        'verified_purchase',
        'is_approved'
    ];

    protected $casts = [
        'rating' => 'integer',
        'verified_purchase' => 'boolean',
        'is_approved' => 'boolean'
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeVerifiedPurchase($query)
    {
        return $query->where('verified_purchase', true);
    }

    // Helper methods
    public static function isValidRating($rating)
    {
        return $rating >= 1 && $rating <= 5;
    }

    public function getFormattedRatingAttribute()
    {
        return number_format($this->rating, 1);
    }
} 