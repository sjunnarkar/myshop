<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class InventoryItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'sku',
        'stock_level',
        'low_stock_threshold',
        'reorder_point',
        'warehouse_location',
        'shelf_location',
        'bin_location',
        'track_inventory',
        'allow_backorders',
        'unit_cost',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'stock_level' => 'integer',
        'low_stock_threshold' => 'integer',
        'reorder_point' => 'integer',
        'track_inventory' => 'boolean',
        'allow_backorders' => 'boolean',
        'unit_cost' => 'decimal:2',
    ];

    /**
     * Get the product that this inventory item belongs to.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the inventory movements for this inventory item.
     */
    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    /**
     * Get the latest inventory movement for this inventory item.
     */
    public function latestMovement(): HasOne
    {
        return $this->hasOne(InventoryMovement::class)->latest();
    }

    /**
     * Check if the item is in stock.
     *
     * @return bool
     */
    public function isInStock(): bool
    {
        if (!$this->track_inventory) {
            return true;
        }
        
        return $this->stock_level > 0;
    }

    /**
     * Check if the item has low stock.
     *
     * @return bool
     */
    public function hasLowStock(): bool
    {
        if (!$this->track_inventory) {
            return false;
        }
        
        return $this->stock_level <= $this->low_stock_threshold && $this->stock_level > 0;
    }

    /**
     * Check if the item is out of stock.
     *
     * @return bool
     */
    public function isOutOfStock(): bool
    {
        if (!$this->track_inventory) {
            return false;
        }
        
        return $this->stock_level <= 0;
    }

    /**
     * Check if the item needs reordering.
     *
     * @return bool
     */
    public function needsReordering(): bool
    {
        if (!$this->track_inventory) {
            return false;
        }
        
        return $this->stock_level <= $this->reorder_point;
    }

    /**
     * Adjust inventory level and record the movement.
     *
     * @param int $quantity Positive for additions, negative for reductions
     * @param string $movementType Type of movement (purchase, sale, adjustment, etc.)
     * @param string|null $referenceType Related model type (Order, PurchaseOrder, etc.)
     * @param int|null $referenceId Related model ID
     * @param string|null $notes Additional notes
     * @param int|null $userId User who made the adjustment
     * @return InventoryMovement
     */
    public function adjustStock(int $quantity, string $movementType, ?string $referenceType = null, ?int $referenceId = null, ?string $notes = null, ?int $userId = null): InventoryMovement
    {
        $stockBefore = $this->stock_level;
        $this->stock_level += $quantity;
        $this->save();
        
        return $this->movements()->create([
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'movement_type' => $movementType,
            'quantity' => $quantity,
            'stock_before' => $stockBefore,
            'stock_after' => $this->stock_level,
            'notes' => $notes,
            'user_id' => $userId ?? auth()->id(),
        ]);
    }

    /**
     * Check if the requested quantity is available.
     *
     * @param int $requestedQuantity
     * @return bool
     */
    public function hasAvailableStock(int $requestedQuantity): bool
    {
        if (!$this->track_inventory) {
            return true;
        }
        
        if ($this->allow_backorders) {
            return true;
        }
        
        return $this->stock_level >= $requestedQuantity;
    }
} 