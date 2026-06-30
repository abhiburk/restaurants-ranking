<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasUuids;
    
    protected $guarded = ['id'];

    protected $casts = [
        'amenities' => 'array',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeToday($query)
    {
        return $query
        ->where('voted_at', '>=', now()->startOfDay())
        ->where('voted_at', '<', now()->endOfDay());
    }
}
