<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'email',
        'first_name',
        'last_name',
        'status', // subscribed, unsubscribed, bounced
        'subscribed_at',
        'unsubscribed_at',
        'last_opened_at',
        'preferences'
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'last_opened_at' => 'datetime',
        'preferences' => 'array'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper Methods
    public function generateVerificationToken()
    {
        $this->verification_token = Str::random(64);
        $this->save();
    }

    public function verify()
    {
        $this->verified_at = now();
        $this->save();
    }

    public function unsubscribe()
    {
        $this->unsubscribed_at = now();
        $this->save();
    }

    public function updatePreferences(array $preferences)
    {
        $this->preferences = $preferences;
        $this->save();
    }

    public function isVerified()
    {
        return !is_null($this->verified_at);
    }

    public function isUnsubscribed()
    {
        return !is_null($this->unsubscribed_at);
    }

    public function isActive()
    {
        return $this->status === 'subscribed';
    }

    // Scopes
    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }

    public function scopeActive($query)
    {
        return $query->whereNotNull('verified_at')
            ->whereNull('unsubscribed_at');
    }

    public function scopeSubscribed($query)
    {
        return $query->where('status', 'subscribed');
    }

    public function scopeUnsubscribed($query)
    {
        return $query->where('status', 'unsubscribed');
    }
} 