<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'address_type',
        'street_address',
        'city',
        'state',
        'postal_code',
        'country',
        'phone',
        'is_default',
        'is_shipping',
        'is_billing'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_default' => 'boolean',
        'is_shipping' => 'boolean',
        'is_billing' => 'boolean'
    ];

    /**
     * Get the user that owns the address.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the full address as a string.
     */
    public function getFullAddressAttribute(): string
    {
        return implode(', ', array_filter([
            $this->street_address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country
        ]));
    }

    /**
     * Set this address as the default shipping address.
     */
    public function setAsDefaultShipping(): void
    {
        $this->user->addresses()
            ->where('id', '!=', $this->id)
            ->where('is_shipping', true)
            ->where('is_default', true)
            ->update(['is_default' => false]);

        $this->update([
            'is_shipping' => true,
            'is_default' => true,
        ]);
    }

    /**
     * Set this address as the default billing address.
     */
    public function setAsDefaultBilling(): void
    {
        $this->user->addresses()
            ->where('id', '!=', $this->id)
            ->where('is_billing', true)
            ->where('is_default', true)
            ->update(['is_default' => false]);

        $this->update([
            'is_billing' => true,
            'is_default' => true,
        ]);
    }
} 