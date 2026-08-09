<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Models\Vote;
use App\Models\VoteArchive;
use App\Services\PulseService;
use App\Services\RestaurantStatsService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

#[Signature('votes:reset-daily')]
#[Description('Snapshot yesterday votes to archive')]
class ResetDailyVotesCommand extends Command
{
    public function handle(): void
    {
        $this->info('Checking for unarchived vote dates...');

        $today = now()->toDateString();

        $unarchivedDates = Vote::select('voted_at')
            ->where('voted_at', '<', $today)
            ->whereNotIn('voted_at', function ($query) {
                $query->select('archived_at')->from('vote_archives');
            })
            ->distinct()
            ->orderBy('voted_at')
            ->pluck('voted_at');

        if ($unarchivedDates->isEmpty()) {
            $this->info('All dates already archived.');
            return;
        }

        $bar = $this->output->createProgressBar($unarchivedDates->count());

        foreach ($unarchivedDates as $date) {
            $this->archiveDate($date);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Archiving completed.');
    }

    private function archiveDate(string $date): void
    {
        /*
        |--------------------------------------------------------------------------
        | Aggregate vote data for that day
        |--------------------------------------------------------------------------
        */

        $votes = Vote::select(
            'restaurant_id',
            DB::raw('MIN(city_id) as city_id'),
            DB::raw('COUNT(*) as total_votes'),
            DB::raw('COUNT(DISTINCT user_id) as unique_voters'),
            DB::raw('MIN(created_at) as first_vote_at'),
            DB::raw('MAX(created_at) as last_vote_at')
        )
            ->where('voted_at', $date)
            ->groupBy('restaurant_id')
            ->orderByDesc('total_votes')
            ->get();

        if ($votes->isEmpty()) {
            $this->warn("No votes found for {$date}");
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Get previous day archives for change calculations
        |--------------------------------------------------------------------------
        */

        $previousDay = now()->parse($date)->subDay()->toDateString();

        $previousStats = VoteArchive::where('archived_at', $previousDay)
            ->pluck('votes', 'restaurant_id');

        $previousRanks = VoteArchive::where('archived_at', $previousDay)
            ->pluck('rank', 'restaurant_id');

        /*
        |--------------------------------------------------------------------------
        | Build archive rows
        |--------------------------------------------------------------------------
        */

        $rows = $votes->map(function ($row, $index) use ($previousStats, $previousRanks, $date) {

            $previousVotes = $previousStats[$row->restaurant_id] ?? 0;
            $previousRank = $previousRanks[$row->restaurant_id] ?? null;

            return [
                'restaurant_id' => $row->restaurant_id,
                'city_id' => $row->city_id,
                'votes' => $row->total_votes,
                'unique_voters' => $row->unique_voters,
                'first_vote_at' => $row->first_vote_at,
                'last_vote_at' => $row->last_vote_at,
                'vote_change' => $row->total_votes - $previousVotes,
                'rank' => $index + 1,
                'rank_change' => $previousRank ? ($previousRank - ($index + 1)) : 0,
                'archived_at' => $date,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        VoteArchive::insert($rows->toArray());

        // After VoteArchive::insert() in archiveDate()
        $pulse = app(PulseService::class);

        // Daily winner event — top restaurant per city
        $winners = $rows->groupBy('city_id')->map(function ($cityRows) {
            return $cityRows->sortByDesc('votes')->first();
        });

        foreach ($winners as $winner) {
            $restaurant = Restaurant::with('city')->find($winner['restaurant_id']);
            if (! $restaurant) continue;

            $pulse->createEvent(
                $restaurant->city_id,
                'daily_winner',
                [
                    'restaurant_name' => $restaurant->name,
                    'total_votes'     => $winner['votes'],
                    'date'            => $date,
                    'area'            => $restaurant->area,
                ],
                $restaurant->id,
                pinned: true  // winner events are pinned
            );

            // Check streak milestone
            $streak = app(RestaurantStatsService::class, ['restaurant' => $restaurant])->currentStreak();

            if (in_array($streak, PulseService::STREAK_MILESTONES)) {
                $pulse->createEvent(
                    $restaurant->city_id,
                    'streak_milestone',
                    [
                        'restaurant_name' => $restaurant->name,
                        'streak_days'     => $streak,
                        'area'            => $restaurant->area,
                    ],
                    $restaurant->id,
                    pinned: true
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Delete votes for that day
        |--------------------------------------------------------------------------
        */

        Vote::where('voted_at', $date)->delete();

        $this->line("Archived {$date} ({$rows->count()} restaurants)");
    }
}
