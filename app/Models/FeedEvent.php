<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedEvent extends Model
{

    use HasUuids;
    
    protected $fillable = [
        'city_id', 'restaurant_id', 'event_type',
        'event_data', 'is_pinned', 'occurred_at', 'expires_at',
    ];

    protected $casts = [
        'event_data'  => 'array',
        'is_pinned'   => 'boolean',
        'occurred_at' => 'datetime',
        'expires_at'  => 'datetime',
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    // ── Scopes ────────────────────────────────────────────────────
    public function scopeForCity($query, string $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    public function scopeVisible($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('occurred_at', '>=', now()->subHours($hours));
    }
}