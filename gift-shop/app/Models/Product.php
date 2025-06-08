<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'base_price',
        'thumbnail',
        'customization_options',
        'is_active',
        'stock',
        'dimensions',
        'printing_areas',
        'additional_images',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'is_active' => 'boolean',
        'stock' => 'integer',
        'customization_options' => 'array',
        'dimensions' => 'array',
        'printing_areas' => 'array',
        'additional_images' => 'array',
    ];

    protected $appends = ['average_rating', 'total_reviews'];

    // Automatically generate slug from name
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($product) {
            if (!$product->slug) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function customizationTemplates()
    {
        return $this->belongsToMany(CustomizationTemplate::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function inventory()
    {
        return $this->hasOne(InventoryItem::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeTopRated($query)
    {
        return $query->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating');
    }

    // Helper methods
    public function isInStock()
    {
        return $this->stock > 0;
    }

    public function decrementStock($quantity = 1)
    {
        if ($this->stock >= $quantity) {
            $this->decrement('stock', $quantity);
            return true;
        }
        return false;
    }

    // Rating methods
    public function getAverageRatingAttribute()
    {
        return $this->reviews()
            ->approved()
            ->avg('rating') ?? 0;
    }

    public function getTotalReviewsAttribute()
    {
        return $this->reviews()
            ->approved()
            ->count();
    }

    public function getRatingDistributionAttribute()
    {
        $distribution = [];
        $totalReviews = $this->total_reviews;

        for ($i = 5; $i >= 1; $i--) {
            $count = $this->reviews()
                ->approved()
                ->where('rating', $i)
                ->count();
            
            $distribution[$i] = [
                'count' => $count,
                'percentage' => $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0
            ];
        }

        return $distribution;
    }

    public function getVerifiedReviewsPercentageAttribute()
    {
        if ($this->total_reviews === 0) {
            return 0;
        }

        $verifiedCount = $this->reviews()
            ->approved()
            ->verifiedPurchase()
            ->count();

        return ($verifiedCount / $this->total_reviews) * 100;
    }

    public function getStockLevelAttribute()
    {
        return $this->inventory ? $this->inventory->stock_level : 0;
    }

    public function getInStockAttribute()
    {
        return $this->stock_level > 0;
    }

    public function getLowStockAttribute()
    {
        return $this->inventory && $this->inventory->stock_level <= $this->inventory->reorder_point;
    }

    public function adjustStock(int $quantity, string $reason, ?string $notes = null, string $type = 'order')
    {
        if (!$this->inventory) {
            return false;
        }

        DB::transaction(function () use ($quantity, $reason, $notes, $type) {
            $this->inventory->stock_level += $quantity;
            $this->inventory->save();

            InventoryMovement::create([
                'inventory_item_id' => $this->inventory->id,
                'quantity' => $quantity,
                'type' => $type,
                'reason' => $reason,
                'notes' => $notes,
                'user_id' => auth()->id() ?? 1
            ]);
        });

        return true;
    }
}
