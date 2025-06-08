<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomizationTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'fields'
    ];

    protected $casts = [
        'fields' => 'array'
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }
}
