<?php

namespace App\Models;

use App\Notifications\NewOrderNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Notification;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'order_number',
        'total_amount',
        'status',
        'payment_status',
        'payment_method',
        'shipping_method',
        'shipping_cost',
        'shipping_address_id',
        'billing_address_id',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    const PAYMENT_STATUS_PENDING = 'pending';
    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_FAILED = 'failed';
    const PAYMENT_STATUS_REFUNDED = 'refunded';

    protected static function booted()
    {
        static::created(function ($order) {
            // Get all admin users
            $admins = User::where('is_admin', true)->get();
            
            // Notify all admins about the new order
            Notification::send($admins, new NewOrderNotification($order));

            // Adjust inventory for each order item
            foreach ($order->items as $item) {
                $item->product->adjustStock(
                    -$item->quantity,
                    "Order #{$order->order_number}",
                    "Automatic stock reduction for order",
                    'order'
                );
            }
        });

        static::updated(function ($order) {
            // If order status changed to cancelled, restore inventory
            if ($order->isDirty('status') && $order->status === 'cancelled') {
                foreach ($order->items as $item) {
                    $item->product->adjustStock(
                        $item->quantity,
                        "Order #{$order->order_number} cancelled",
                        "Stock restored due to order cancellation",
                        'return'
                    );
                }
            }
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shippingAddress()
    {
        return $this->belongsTo(UserAddress::class, 'shipping_address_id');
    }

    public function billingAddress()
    {
        return $this->belongsTo(UserAddress::class, 'billing_address_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', self::STATUS_PROCESSING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    // Helper methods
    public function calculateTotal()
    {
        $itemsTotal = $this->items->sum('subtotal');
        $this->total_amount = $itemsTotal + $this->shipping_cost;
        return $this->total_amount;
    }

    public function markAsPaid()
    {
        $this->payment_status = self::PAYMENT_STATUS_PAID;
        $this->status = self::STATUS_PROCESSING;
        $this->save();
    }

    public function markAsCompleted()
    {
        $this->status = self::STATUS_COMPLETED;
        $this->save();
    }

    public function markAsCancelled()
    {
        $this->status = self::STATUS_CANCELLED;
        $this->save();
    }

    /**
     * Get the color class for the order status badge.
     *
     * @return string
     */
    public function getStatusColorAttribute()
    {
        switch ($this->status) {
            case self::STATUS_PENDING:
                return 'warning';
            case self::STATUS_PROCESSING:
                return 'info';
            case self::STATUS_COMPLETED:
                return 'success';
            case self::STATUS_CANCELLED:
                return 'danger';
            default:
                return 'secondary';
        }
    }
}
