<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'is_active',
        'order',
        'show_in_footer',
        'show_in_header',
        'layout'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_in_footer' => 'boolean',
        'show_in_header' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($page) {
            if (!$page->slug) {
                $page->slug = Str::slug($page->title);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function scopeHeader($query)
    {
        return $query->where('show_in_header', true);
    }

    public function scopeFooter($query)
    {
        return $query->where('show_in_footer', true);
    }

    public function getUrlAttribute()
    {
        return route('pages.show', $this->slug);
    }
} 