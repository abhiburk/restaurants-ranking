<?php

namespace App\Jobs;

use App\Models\Restaurant;
use App\Models\Vote;
use App\Services\PulseService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class CheckPulseEventsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public function __construct(
        private string $restaurantId
    ) {}

    public function handle(PulseService $pulse): void
    {
        $restaurant = Restaurant::with('city')->find($this->restaurantId);

        if (! $restaurant) return;

        $today = now()->timezone('Asia/Kolkata')->toDateString();

        // Today's vote count for this restaurant
        $votesToday = Vote::where('restaurant_id', $this->restaurantId)
            ->where('voted_at', $today)
            ->count();

        // Today's rank in the city
        $rank = Vote::select('restaurant_id', DB::raw('COUNT(*) as total'))
            ->whereHas(
                'restaurant',
                fn($q) =>
                $q->where('city_id', $restaurant->city_id)->whereNull('deleted_at')
            )
            ->where('voted_at', $today)
            ->groupBy('restaurant_id')
            ->orderByDesc('total')
            ->pluck('restaurant_id')
            ->values()
            ->search($this->restaurantId);

        $rank = $rank !== false ? $rank + 1 : null;

        // Check first vote — new entry event
        if ($votesToday === 1) {
            $isNewRestaurant = Vote::where('restaurant_id', $this->restaurantId)
                ->where('voted_at', '<', $today)
                ->doesntExist();

            if ($isNewRestaurant) {
                $pulse->createEvent(
                    $restaurant->city_id,
                    'new_entry',
                    [
                        'restaurant_name' => $restaurant->name,
                        'area'            => $restaurant->area,
                        'category'        => $restaurant->category?->name,
                    ],
                    $restaurant->id
                );
            }
        }

        // Check vote milestone
        $pulse->checkVoteMilestone($restaurant, $votesToday);

        // Check rank change (entered top 3)
        if ($rank) {
            $pulse->checkRankChange($restaurant, $rank);
        }

        // Check trending
        $pulse->checkTrending($restaurant);
    }
}
