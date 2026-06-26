<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vote extends Model
{
    use HasUuids, SoftDeletes;
    
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
        return $query->whereBetween(
            'voted_at',
            [now()->startOfDay(), now()->endOfDay()]
        );
    }
}
