<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Campaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'description',
        'budget',
        'cost',
        'reach',
        'impressions',
        'clicks',
        'conversions',
        'revenue',
        'status',
        'start_date',
        'end_date',
        'targeting_criteria',
        'platforms',
        'notes',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'cost' => 'decimal:2',
        'revenue' => 'decimal:2',
        'reach' => 'integer',
        'impressions' => 'integer',
        'clicks' => 'integer',
        'conversions' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'targeting_criteria' => 'array',
        'platforms' => 'array',
    ];

    // Relationships
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function conversions()
    {
        return $this->hasMany(CampaignConversion::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('start_date', [$startDate, $endDate])
            ->orWhereBetween('end_date', [$startDate, $endDate])
            ->orWhere(function ($q) use ($startDate, $endDate) {
                $q->where('start_date', '<=', $startDate)
                    ->where('end_date', '>=', $endDate);
            });
    }

    // Helper Methods
    public function calculateROI()
    {
        if ($this->cost <= 0) {
            return 0;
        }
        return (($this->revenue - $this->cost) / $this->cost) * 100;
    }

    public function getConversionRate()
    {
        if ($this->clicks <= 0) {
            return 0;
        }
        return ($this->conversions / $this->clicks) * 100;
    }

    public function getCTR()
    {
        if ($this->impressions <= 0) {
            return 0;
        }
        return ($this->clicks / $this->impressions) * 100;
    }

    public function getCostPerClick()
    {
        if ($this->clicks <= 0) {
            return 0;
        }
        return $this->cost / $this->clicks;
    }

    public function getCostPerConversion()
    {
        if ($this->conversions <= 0) {
            return 0;
        }
        return $this->cost / $this->conversions;
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isScheduled()
    {
        return $this->status === 'scheduled';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function getDuration()
    {
        if (!$this->start_date || !$this->end_date) {
            return null;
        }
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function getProgress()
    {
        if (!$this->start_date || !$this->end_date || $this->status === 'draft') {
            return 0;
        }

        $now = Carbon::now();
        $total = $this->start_date->diffInSeconds($this->end_date);
        $elapsed = $this->start_date->diffInSeconds($now);

        if ($now < $this->start_date) {
            return 0;
        }

        if ($now > $this->end_date) {
            return 100;
        }

        return min(100, round(($elapsed / $total) * 100));
    }
} 