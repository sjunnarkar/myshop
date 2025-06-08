<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'type',
        'value',
        'buy_x',
        'get_y',
        'is_active',
        'starts_at',
        'expires_at',
        'applicable_products',
        'applicable_categories',
        'minimum_spend',
        'maximum_discount',
        'usage_limit_per_user',
        'stackable',
        'priority',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'minimum_spend' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'applicable_products' => 'array',
        'applicable_categories' => 'array',
        'stackable' => 'boolean',
        'priority' => 'integer',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    // Helper Methods
    public function isValid()
    {
        return $this->is_active &&
            (!$this->starts_at || $this->starts_at <= now()) &&
            (!$this->expires_at || $this->expires_at > now());
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at <= now();
    }

    public function calculateDiscount($items)
    {
        if (!$this->isValid()) {
            return 0;
        }

        $subtotal = collect($items)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        if ($this->minimum_spend && $subtotal < $this->minimum_spend) {
            return 0;
        }

        $discount = 0;

        switch ($this->type) {
            case 'percentage':
                $discount = $subtotal * ($this->value / 100);
                break;

            case 'fixed':
                $discount = $this->value;
                break;

            case 'buy_x_get_y':
                $discount = $this->calculateBuyXGetYDiscount($items);
                break;
        }

        if ($this->maximum_discount) {
            $discount = min($discount, $this->maximum_discount);
        }

        return $discount;
    }

    protected function calculateBuyXGetYDiscount($items)
    {
        if (!$this->buy_x || !$this->get_y) {
            return 0;
        }

        $totalQuantity = collect($items)->sum('quantity');
        $sets = floor($totalQuantity / ($this->buy_x + $this->get_y));
        
        if ($sets <= 0) {
            return 0;
        }

        // Calculate the average price of items
        $averagePrice = collect($items)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        }) / $totalQuantity;

        return $sets * $this->get_y * $averagePrice;
    }

    public function isValidForProducts(array $productIds)
    {
        if (!$this->applicable_products) {
            return true;
        }

        return count(array_intersect($productIds, $this->applicable_products)) > 0;
    }

    public function isValidForCategories(array $categoryIds)
    {
        if (!$this->applicable_categories) {
            return true;
        }

        return count(array_intersect($categoryIds, $this->applicable_categories)) > 0;
    }

    public function hasReachedUserLimit($userId)
    {
        if (!$this->usage_limit_per_user) {
            return false;
        }

        $usageCount = Order::where('user_id', $userId)
            ->whereJsonContains('applied_discounts', $this->id)
            ->count();

        return $usageCount >= $this->usage_limit_per_user;
    }
} 