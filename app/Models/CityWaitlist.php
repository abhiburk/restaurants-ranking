<?php

namespace App\Models;

use App\Notifications\CityAvailableNotification;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;

class CityWaitlist extends Model
{
    use HasUuids;

    protected $guarded = [
        'id',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sendNotification()
    {
        Notification::route('mail', $this->email)->notify(new CityAvailableNotification($this->city));

        $this->sent_at = now();
        $this->save();
    }
}
