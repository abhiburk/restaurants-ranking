<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantView extends Model
{
    protected $guarded = ['id'];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->ip_address = request()->ip();
            $model->visitor_id = request()->cookie('visitor_id');
            $model->user_id = auth()->id();
        });

        static::created(function ($model) {
            $model->restaurant->increment('views');
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
