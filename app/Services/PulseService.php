<?php

namespace App\Services;

use App\Models\FeedEvent;
use App\Models\Restaurant;
use App\Models\Vote;
use App\Models\VoteArchive;

class PulseService
{
    // TTL per event type in hours — null = permanent
    const TTL = [
        'rank_change'      => 24,
        'trending'         => 6,
        'vote_milestone'   => 24,
        'streak_milestone' => null,
        'daily_winner'     => null,
        'new_entry'        => 48,
        'city_pulse'       => 2,
    ];

    // Vote milestone thresholds
    const VOTE_MILESTONES = [100, 250, 500, 1000, 2500, 5000];

    // Streak milestones
    const STREAK_MILESTONES = [3, 5, 7, 10, 14, 30];

    // ── Create a feed event with dedup guard ──────────────────────
    public function createEvent(
        string $cityId,
        string $eventType,
        array  $data,
        ?string $restaurantId = null,
        bool   $pinned = false
    ): ?FeedEvent {

        // Dedup — don't create the same event type for the
        // same restaurant twice within the TTL window
        if ($restaurantId && $this->isDuplicate($cityId, $restaurantId, $eventType)) {
            return null;
        }

        $ttlHours  = self::TTL[$eventType] ?? 24;
        $expiresAt = $ttlHours ? now()->addHours($ttlHours) : null;

        return FeedEvent::create([
            'city_id'       => $cityId,
            'restaurant_id' => $restaurantId,
            'event_type'    => $eventType,
            'event_data'    => $data,
            'is_pinned'     => $pinned,
            'occurred_at'   => now(),
            'expires_at'    => $expiresAt,
        ]);
    }

    // ── Get paginated feed for a city ─────────────────────────────
    public function getFeed(int $limit = 10, string $cityId = null)
    {
        return FeedEvent::with('restaurant:id,name,slug', 'city:id,name,slug')
            ->when($cityId, fn($q) => $q->forCity($cityId))
            ->visible()
            ->orderByDesc('is_pinned')
            ->orderByDesc('occurred_at')
            ->limit($limit)
            ->get();
    }

    // ── City pulse score 0-100 ────────────────────────────────────
    public function getPulseScore(string $cityId = null): int
    {
        $votesLastHour = Vote::when($cityId, function ($q) use ($cityId) {
            $q->whereHas('restaurant', fn($q) => $q->where('city_id', $cityId));
        })
            ->where('created_at', '>=', now()->subHour())
            ->count();

        // Get historical average votes per hour for this city
        // Using last 30 days average
        // Sum all votes for the city over the last 30 days
        $totalVotes = VoteArchive::when($cityId, function ($q) use ($cityId) {
            $q->whereHas('restaurant', fn($q) => $q->where('city_id', $cityId));
        })
            ->where('archived_at', '>=', now()->subDays(30))
            ->sum('votes');

        // Divide by total hours in 30 days
        $avgPerHour = $totalVotes / (30 * 24);

        if ($avgPerHour === 0.0) {
            return $votesLastHour > 0 ? 50 : 0;
        }

        // Score = current / average * 50, capped at 100
        return min(100, (int) round(($votesLastHour / $avgPerHour) * 50));
    }

    // ── Check vote milestones ─────────────────────────────────────
    public function checkVoteMilestone(Restaurant $restaurant, int $currentVotes): void
    {
        foreach (self::VOTE_MILESTONES as $milestone) {
            if ($currentVotes >= $milestone && ($currentVotes - 1) < $milestone) {
                $this->createEvent(
                    $restaurant->city_id,
                    'vote_milestone',
                    [
                        'restaurant_name' => $restaurant->name,
                        'milestone'       => $milestone,
                        'total_votes'     => $currentVotes,
                    ],
                    $restaurant->id
                );
                break; // only one milestone per vote
            }
        }
    }

    // ── Check rank change (entering top 3) ───────────────────────
    public function checkRankChange(Restaurant $restaurant, int $newRank): void
    {
        if ($newRank > 3) return;

        // Only fire if they weren't in top 3 already today
        $alreadyFired = FeedEvent::where('restaurant_id', $restaurant->id)
            ->where('event_type', 'rank_change')
            ->whereDate('occurred_at', today())
            ->where('event_data->rank', $newRank)
            ->exists();

        if ($alreadyFired) return;

        $this->createEvent(
            $restaurant->city_id,
            'rank_change',
            [
                'restaurant_name' => $restaurant->name,
                'rank'            => $newRank,
                'area'            => $restaurant->area,
            ],
            $restaurant->id
        );
    }

    // ── Check trending ────────────────────────────────────────────
    public function checkTrending(Restaurant $restaurant): void
    {
        $recent   = Vote::where('restaurant_id', $restaurant->id)
            ->where('created_at', '>=', now()->subHours(2))
            ->count();

        $previous = Vote::where('restaurant_id', $restaurant->id)
            ->whereBetween('created_at', [now()->subHours(4), now()->subHours(2)])
            ->count();

        $isTrending = $recent >= 10 && (
            $previous === 0 ? true : ($recent / $previous) >= 2.0
        );

        if (! $isTrending) return;

        // Only one trending event per restaurant per 6 hours
        $recentTrending = FeedEvent::where('restaurant_id', $restaurant->id)
            ->where('event_type', 'trending')
            ->where('occurred_at', '>=', now()->subHours(6))
            ->exists();

        if ($recentTrending) return;

        $this->createEvent(
            $restaurant->city_id,
            'trending',
            [
                'restaurant_name' => $restaurant->name,
                'votes_last_2hrs' => $recent,
                'area'            => $restaurant->area,
            ],
            $restaurant->id
        );
    }

    // ── Dedup guard ───────────────────────────────────────────────
    private function isDuplicate(
        string $cityId,
        string $restaurantId,
        string $eventType
    ): bool {
        $ttl = self::TTL[$eventType] ?? 24;

        return FeedEvent::where('city_id', $cityId)
            ->where('restaurant_id', $restaurantId)
            ->where('event_type', $eventType)
            ->where('occurred_at', '>=', now()->subHours($ttl))
            ->exists();
    }
}
