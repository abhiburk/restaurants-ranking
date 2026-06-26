<?php

namespace App\Services;

use App\Models\Restaurant;
use App\Models\Vote;
use App\Models\VoteArchive;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;

class RestaurantStatsService
{
    private string $today;

    public function __construct(private Restaurant $restaurant)
    {
        $this->today = now()->toDateString();
    }

    // Todays votes
    public function votesToday(): int
    {
        return Vote::where('restaurant_id', $this->restaurant->id)->whereDate('voted_at', $this->today)->count();
    }

    public function votesYesterday(): int
    {
        $yesterdayStart = now()->subDay()->startOfDay();
        $yesterdayEnd = now()->subDay()->endOfDay();
        return VoteArchive::where('restaurant_id', $this->restaurant->id)->whereBetween('archived_at', [$yesterdayStart, $yesterdayEnd])->sum('votes');
    }

    public function growthPercentage(): int
    {
        $votesYesterday = $this->votesYesterday();

        if ($votesYesterday === 0) {
            return 100;
        }
        return ceil(($this->votesToday() - $votesYesterday) / $votesYesterday * 100);
    }

    // Today's rank within the city
    public function rank(): int
    {
        $cityRanks = $this->cityLeaderboard();

        $position = array_search(
            $this->restaurant->id,
            array_keys($cityRanks->toArray())
        );

        return $position !== false ? $position + 1 : $cityRanks->count() + 1;
    }

    // Total restaurants ranked today in the city
    public function totalRanked(): int
    {
        return max($this->cityLeaderboard()->count(), $this->rank());
    }

    // Votes in the last hour
    public function votesLastHour(): int
    {
        return Vote::where('restaurant_id', $this->restaurant->id)->where('created_at', '>=', now()->subHour())->count();
    }

    // Hourly change direction and percentage
    public function hourlyChange(): array
    {
        $lastHour = now()->subHour();
        $twoHrsAgo = now()->subHours(2);

        $current = $this->votesLastHour(); // votes in the last hour

        // votes in the previous hour (2 hours ago to 1 hour ago)
        $previous = Vote::where('restaurant_id', $this->restaurant->id)->whereBetween('created_at', [$twoHrsAgo, $lastHour])->count();

        if ($previous === 0) {
            return [
                'percent' => $current > 0 ? 100 : 0,
                'direction' => $current > 0 ? 'up' : 'neutral',
                'last_hour_votes' => $current ?? 0,
                'prev_hour_votes' => $previous ?? 0,
            ];
        }

        $percent = round((($current - $previous) / $previous) * 100);

        return [
            'percent' => abs($percent),
            'direction' => match (true) {
                $percent > 0 => 'up',
                $percent < 0 => 'down',
                default => 'neutral',
            },
            'last_hour_votes' => $current,
            'prev_hour_votes' => $previous,
        ];
    }

    // All time stats — mix of archive + today's live votes
    public function allTime(): array
    {
        // Best single day from archive (past days)
        $bestArchived = VoteArchive::where('restaurant_id', $this->restaurant->id)->orderByDesc('votes')->first();

        // Today's live count (not yet archived)
        $todayVotes = $this->votesToday();

        // Compare to see if today is the best day
        $bestDayVotes = max($bestArchived?->votes ?? 0, $todayVotes);
        $bestDayDate = $bestDayVotes === $todayVotes && $todayVotes >= ($bestArchived?->votes ?? 0) ? now()->toDateString() : $bestArchived?->archived_at;

        // Total votes — count all raw votes (archive is summary, not raw count)
        $totalVotes = Vote::where('restaurant_id', $this->restaurant->id)->count();

        // Days on board — count distinct days from archive + today if has votes
        $daysOnBoard = VoteArchive::where('restaurant_id', $this->restaurant->id)->count();

        if ($todayVotes > 0) {
            $daysOnBoard++;
        }

        return [
            'best_rank' => $this->bestRankEver(),
            'best_day_votes' => $bestDayVotes,
            'best_day_date' => $bestDayDate,
            'best_day_label' => $bestDayDate ? (Carbon::parse($bestDayDate)->isToday() ? 'Today' : Carbon::parse($bestDayDate)->format('d M')) : null,
            'total_votes' => Number::abbreviate($totalVotes, 1),
            'days_on_board' => $daysOnBoard,
        ];
    }

    // Best rank ever from archive
    private function bestRankEver(): int
    {
        $best = VoteArchive::where('restaurant_id', $this->restaurant->id)->min('rank');

        return $best ?? 0;
    }

    // Rank change vs yesterday
    public function rankChange(): int
    {
        $yesterday = now()->subDay()->toDateString();

        $yesterdayRanks = Vote::select(
            'restaurant_id',
            DB::raw('COUNT(*) as total')
        )->whereHas(
            'restaurant',
            fn($q) => $q->where('city_id', $this->restaurant->city_id)
        )
            ->where('voted_at', $yesterday)
            ->groupBy('restaurant_id')
            ->orderByDesc('total')
            ->pluck('total', 'restaurant_id');

        $todayRank = $this->rank();
        $yesterdayRank = array_search(
            $this->restaurant->id,
            array_keys($yesterdayRanks->toArray())
        );
        $yesterdayRank = $yesterdayRank !== false ? $yesterdayRank + 1 : null;

        if (! $yesterdayRank) {
            return 0;
        }

        return $yesterdayRank - $todayRank; // positive = moved up
    }

    // Private: ordered city leaderboard for today
    private function cityLeaderboard(): Collection
    {
        return once(function () {
            return Vote::select(
                'restaurant_id',
                DB::raw('COUNT(*) as total')
            )->whereHas(
                'restaurant',
                fn($q) => $q->where('city_id', $this->restaurant->city_id)->active()
            )
                ->where('voted_at', $this->today)
                ->groupBy('restaurant_id')
                ->orderByDesc('total')
                ->pluck('total', 'restaurant_id');
        });
    }

    // "Trending" = vote velocity in the last 2 hours is significantly
    // higher than the 2 hours before that, AND above a minimum threshold.
    public function isTrendingToday(): bool
    {
        $now = now();
        $twoHrsAgo = $now->copy()->subHours(2);
        $fourHrsAgo = $now->copy()->subHours(4);

        $recentVotes = Vote::where('restaurant_id', $this->restaurant->id)->where('created_at', '>=', $twoHrsAgo)->count();

        // Must have meaningful activity — not just 1 vote vs 0
        if ($recentVotes < 30) {
            return false;
        }

        $previousVotes = Vote::where('restaurant_id', $this->restaurant->id)->whereBetween('created_at', [$fourHrsAgo, $twoHrsAgo])->count();

        // Trending if recent window is 50%+ higher than previous window
        if ($previousVotes === 0) {
            return true;
        }

        return ($recentVotes / $previousVotes) >= 2.0;
    }

    // Counts consecutive days going backwards from yesterday where this
    // restaurant ranked #1. Today is excluded — the day isn't over yet.
    public function currentStreak(): int
    {
        // Pull consecutive #1 finishes from archive going backwards from yesterday
        $archives = VoteArchive::where('restaurant_id', $this->restaurant->id)
            ->where('rank', 1)
            ->where('archived_at', '<', now()->toDateString())
            ->orderByDesc('archived_at')
            ->pluck('archived_at');

        if ($archives->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $checkDate = now()->subDay()->toDateString();

        foreach ($archives as $date) {
            if ($date !== $checkDate) {
                break;
            }
            $streak++;
            $checkDate = Carbon::parse($checkDate)->subDay()->toDateString();
        }

        return $streak;
    }

    // Previous rank (yesterday) — for "up from #N"
    // Returns the rank this restaurant held yesterday, or null if they
    // had no votes yesterday (new entry or day off).
    public function previousRank(): ?int
    {
        $yesterday = now()->subDay()->toDateString();

        $yesterdayRanks = Vote::select(
            'restaurant_id',
            DB::raw('COUNT(*) as total')
        )->whereHas(
            'restaurant',
            fn($q) => $q->where('city_id', $this->restaurant->city_id)
        )
            ->where('voted_at', $yesterday)
            ->groupBy('restaurant_id')
            ->orderByDesc('total')
            ->pluck('total', 'restaurant_id');

        $position = array_search(
            $this->restaurant->id,
            array_keys($yesterdayRanks->toArray())
        );

        return $position !== false ? $position + 1 : null;
    }

    // Rank movement label — "Up from #3", "Down from #1", "New entry"
    public function rankMovementLabel(): string
    {
        $previousRank = $this->previousRank();
        $currentRank = $this->rank();

        if ($previousRank === null) {
            return 'New';
        }

        if ($previousRank === $currentRank) {
            return "{$currentRank}";
        }

        if ($previousRank > $currentRank) {
            return "{$previousRank}";
        }

        return "{$previousRank}";
    }

    /**
     * Get the last 7 days of votes for this restaurant.
     * The data is a mix of archived votes and today's live votes.
     * The returned array has the following structure:
     **/
    public function votingHistoryInDays(int $days = 6): array
    {
        $today = now()->toDateString();
        $sixDaysAgo = now()->subDays($days)->toDateString();

        // Past 6 days from archive
        $archived = VoteArchive::where('restaurant_id', $this->restaurant->id)
            ->whereBetween('archived_at', [$sixDaysAgo, now()->subDay()->toDateString()])
            ->orderBy('archived_at')
            ->get()
            ->keyBy('archived_at');

        $period = collect();

        for ($i = 6; $i >= 1; $i--) {
            $date = now()->subDays($i)->toDateString();
            $record = $archived->get($date);
            $period->push([
                'date' => $date,
                'label' => Carbon::parse($date)->format('D'),
                'votes' => $record?->votes ?? 0,
                'is_today' => false,
            ]);
        }

        // Today from live votes
        $period->push([
            'date' => $today,
            'label' => Carbon::parse($today)->format('D'),
            'votes' => $this->votesToday(),
            'is_today' => true,
        ]);

        return $period->toArray();
    }

    // Get stats for a collection of restaurants
    public static function forListing(Collection $restaurants): Collection
    {
        if ($restaurants->isEmpty()) {
            return collect();
        }

        $restaurantIds = $restaurants->pluck('id');
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        $yesterdayStart = now()->subDay()->startOfDay();
        $yesterdayEnd = now()->subDay()->endOfDay();
        $twoHrsAgo = now()->subHours(2);
        $fourHrsAgo = now()->subHours(4);

        // Query 1: Today's live vote counts
        // Still from votes table — today isn't archived yet
        $votesToday = Vote::select('restaurant_id', DB::raw('COUNT(*) as total'))
            ->whereIn('restaurant_id', $restaurantIds)
            ->whereBetween('voted_at', [$todayStart, $todayEnd])
            ->groupBy('restaurant_id')
            ->pluck('total', 'restaurant_id');

        $votesYesterday = DB::table('vote_archives')
            ->selectRaw('vote_archives.restaurant_id, SUM(vote_archives.votes) as votes_yesterday')
            ->whereIn('vote_archives.restaurant_id', $restaurantIds)
            ->whereBetween('vote_archives.archived_at', [
                $yesterdayStart,
                $yesterdayEnd
            ])
            ->groupBy('vote_archives.restaurant_id')
            ->pluck('votes_yesterday', 'restaurant_id');

        // Query 2: Today's rank from the FULL city leaderboard
        // Must query all restaurants in the city, not just current page
        // so rank is accurate even on page 2, 3 etc.
        $cityId = $restaurants->first()->city_id;

        $fullCityRanks = Vote::select('restaurant_id', DB::raw('COUNT(*) as total'))
            ->whereHas('restaurant', fn($q) => $q->where('city_id', $cityId)->active())
            ->whereBetween('voted_at', [$todayStart, $todayEnd])
            ->groupBy('restaurant_id')
            ->orderByDesc(DB::raw('COUNT(*)'))
            ->pluck('restaurant_id')
            ->values()
            ->flip() // restaurant_id => zero-based index
            ->map(fn($i) => $i + 1); // convert to 1-based rank

        // Query 3: Trending — last 2hrs vs previous 2hrs
        // Live from votes table — archive doesn't have hourly breakdown
        // $lastTwoHours = Vote::select('restaurant_id', DB::raw('COUNT(*) as total'))
        //     ->whereIn('restaurant_id', $restaurantIds)
        //     ->where('created_at', '>=', $twoHrsAgo)
        //     ->groupBy('restaurant_id')
        //     ->pluck('total', 'restaurant_id');

        // $prevTwoHours = Vote::select('restaurant_id', DB::raw('COUNT(*) as total'))
        //     ->whereIn('restaurant_id', $restaurantIds)
        //     ->whereBetween('created_at', [$fourHrsAgo, $twoHrsAgo])
        //     ->groupBy('restaurant_id')
        //     ->pluck('total', 'restaurant_id');

        // Query 4: Yesterday's rank from archive
        // Used to calculate rank movement (up/down) on listing cards
        $yesterday = now()->subDay()->toDateString();

        $yesterdayRanks = VoteArchive::whereIn('restaurant_id', $restaurantIds)
            ->where('archived_at', $yesterday)
            ->pluck('rank', 'restaurant_id');

        // Map everything onto each restaurant
        return $restaurants->map(function ($restaurant) use (
            $votesToday,
            $votesYesterday,
            $fullCityRanks,
            $yesterdayRanks,
        ) {
            $id = $restaurant->id;
            $votesToday = $votesToday->get($id, 0);
            $votesYesterday = $votesYesterday->get($id, 0);
            // $recent = $lastTwoHours->get($id, 0);
            // $previous = $prevTwoHours->get($id, 0);
            $todayRank = $fullCityRanks->get($id, $fullCityRanks->count() + 1);
            $yesterdayRank = $yesterdayRanks->get($id);

            // $isTrending = $recent >= 10 && ( $previous === 0 ? $recent > 0 : ($recent / $previous) >= 2.0 );

            // Rank change: positive = moved up, negative = moved down
            $rankChange = $yesterdayRank ? $yesterdayRank - $todayRank : null;

            $rankMovement = match (true) {
                $rankChange === null => 'new',
                $rankChange > 0 => 'up',
                $rankChange < 0 => 'down',
                default => 'same',
            };

            return [
                'restaurant' => $restaurant,
                'votes_today' => $votesToday,
                'votes_yesterday' => $votesYesterday,
                'rank' => $todayRank,
                'rank_change' => $rankChange,
                'rank_movement' => $rankMovement, // 'up' | 'down' | 'same' | 'new'
                // 'is_trending' => $isTrending,
                // 'last_two_hours' => $recent,
                // 'prev_two_hours' => $previous,
            ];
        });
    }
}
