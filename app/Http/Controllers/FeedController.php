<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Services\PulseService;
use Illuminate\Support\Facades\Cache;

class FeedController extends Controller
{
    public function index(City $city, PulseService $pulse)
    {
        // $feed = Cache::remember(
        //     "pulse:feed:{$city->id}",
        //     60, // 60 second cache
        //     fn() => $pulse->getFeed($city->id)
        // );

        // $pulseScore = Cache::remember(
        //     "pulse:score:{$city->id}",
        //     300, // 5 minute cache
        //     fn() => $pulse->getPulseScore($city->id)
        // );

        $feed = $pulse->getFeed();
        $pulseScore = $pulse->getPulseScore();

        return response()->json([
            'pulse_score' => $pulseScore,
            'feed'        => $feed->map(fn($event) => [
                'id'              => $event->id,
                'city_name'       => $event->city->name,
                'city_slug'       => $event->city->slug,
                'type'            => $event->event_type,
                'data'            => $event->event_data,
                'restaurant_slug' => $event->restaurant?->slug,
                'is_pinned'       => $event->is_pinned,
                'occurred_at'     => $event->occurred_at->diffForHumans(),
            ]),
        ]);
    }
}
