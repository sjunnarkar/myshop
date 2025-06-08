<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'description',
        'discount_type', // percentage, fixed
        'value',
        'min_purchase',
        'max_discount',
        'usage_limit',
        'usage_count',
        'is_active',
        'starts_at',
        'expires_at',
        'conditions'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'conditions' => 'array',
        'value' => 'decimal:2',
        'min_purchase' => 'decimal:2',
        'max_discount' => 'decimal:2'
    ];

    // Relationships
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

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
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit')
                    ->orWhere('usage_count', '<', 'usage_limit');
            });
    }

    // Helper Methods
    public function isValid()
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        $now = now();
        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }

        if ($this->expires_at && $now->gt($this->expires_at)) {
            return false;
        }

        return true;
    }

    public function calculateDiscount($amount)
    {
        if ($this->discount_type === 'percentage') {
            $discount = $amount * ($this->value / 100);
        } else {
            $discount = $this->value;
        }

        if ($this->max_discount) {
            $discount = min($discount, $this->max_discount);
        }

        return $discount;
    }

    public function incrementUsage()
    {
        $this->increment('usage_count');
    }
} 