<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InventoryMovement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'inventory_item_id',
        'reference_type',
        'reference_id',
        'movement_type',
        'quantity',
        'stock_before',
        'stock_after',
        'notes',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'stock_before' => 'integer',
        'stock_after' => 'integer',
    ];

    /**
     * Get the inventory item that this movement belongs to.
     */
    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    /**
     * Get the user who made this movement.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the reference model (order, purchase order, etc.) that caused this movement.
     */
    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the formatted movement type.
     *
     * @return string
     */
    public function getFormattedMovementTypeAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->movement_type));
    }

    /**
     * Get the label class for the movement type.
     *
     * @return string
     */
    public function getMovementTypeLabelClassAttribute(): string
    {
        return match ($this->movement_type) {
            'purchase' => 'bg-success',
            'sale' => 'bg-primary',
            'return' => 'bg-info',
            'adjustment_add' => 'bg-success',
            'adjustment_subtract' => 'bg-danger',
            'transfer_in' => 'bg-warning',
            'transfer_out' => 'bg-warning',
            'damaged' => 'bg-danger',
            'expired' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    /**
     * Scope a query to only include movements with positive quantity.
     */
    public function scopeAdditions($query)
    {
        return $query->where('quantity', '>', 0);
    }

    /**
     * Scope a query to only include movements with negative quantity.
     */
    public function scopeSubtractions($query)
    {
        return $query->where('quantity', '<', 0);
    }

    /**
     * Scope a query to only include movements of a given type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('movement_type', $type);
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
} 