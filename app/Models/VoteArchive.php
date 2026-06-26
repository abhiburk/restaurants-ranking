<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class VoteArchive extends Model
{
    use HasUuids;

    protected $guarded = ['id'];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeYesterday($query)
    {
        return $query->whereBetween(
            'archived_at',
            [
                now()->subDay()->startOfDay(),
                now()->subDay()->endOfDay(),
            ]
        );
    }
}
