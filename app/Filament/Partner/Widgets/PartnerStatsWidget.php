<?php

namespace App\Filament\Partner\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Restaurant;
use App\Models\Vote;
use App\Models\VoteArchive;
use Illuminate\Support\Number;

class PartnerStatsWidget extends StatsOverviewWidget
{
    
    protected function getStats(): array
    {
        $restaurantIds = Restaurant::where('user_id', auth()->id())->pluck('id');

        /*
        |--------------------------------------------------------------------------
        | Today Votes (live table)
        |--------------------------------------------------------------------------
        */

        $todayVotes = Vote::whereIn('restaurant_id', $restaurantIds)
            ->whereDate('voted_at', today())
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Yesterday Votes (archive table)
        |--------------------------------------------------------------------------
        */

        $yesterdayVotes = VoteArchive::whereIn('restaurant_id', $restaurantIds)
            ->whereDate('archived_at', today()->subDay())
            ->sum('votes');

        /*
        |--------------------------------------------------------------------------
        | Weekly Votes (archive)
        |--------------------------------------------------------------------------
        */

        $weeklyVotes = VoteArchive::whereIn('restaurant_id', $restaurantIds)
            ->whereBetween('archived_at', [now()->subDays(7), now()->subDay()])
            ->sum('votes');

        /*
        |--------------------------------------------------------------------------
        | Total Votes (archive + today)
        |--------------------------------------------------------------------------
        */

        // $totalVotes = VoteArchive::whereIn('restaurant_id', $restaurantIds)
        //     ->sum('votes') + $todayVotes;

        /*
        |--------------------------------------------------------------------------
        | Weekly Chart
        |--------------------------------------------------------------------------
        */

        $weekChart = VoteArchive::whereIn('restaurant_id', $restaurantIds)
            ->whereBetween('archived_at', [now()->subDays(7), now()->subDay()])
            ->selectRaw('archived_at, SUM(votes) as total')
            ->groupBy('archived_at')
            ->orderBy('archived_at')
            ->pluck('total')
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | Today's Hourly Chart
        |--------------------------------------------------------------------------
        */

        $todayChart = Vote::whereIn('restaurant_id', $restaurantIds)
            ->whereDate('created_at', today())
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as total')
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('total')
            ->toArray();
        
        /*
        |--------------------------------------------------------------------------
        | Yesterday Chart - Taken from Vote as VoteArchive table will have only a row with votes sumed. 
        | So it will return just a single row
        |--------------------------------------------------------------------------
        */

        $yesterdayChart = Vote::whereIn('restaurant_id', $restaurantIds)
            ->whereDate('created_at', today()->subDay())
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as total')
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('total')
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | Yesterday Rank
        |--------------------------------------------------------------------------
        */

        // $bestRank = VoteArchive::whereIn('restaurant_id', $restaurantIds)
        //     ->whereDate('archived_at', today()->subDay())
        //     ->orderBy('rank', 'asc')
        //     ->first();

        $votesThisMonth = Vote::whereIn('restaurant_id', $restaurantIds)
            ->whereMonth('voted_at', now()->month)
            ->whereYear('voted_at', now()->year)
            ->count();

        $totalRestaurants = $restaurantIds->count();

        $trend = $todayVotes - $yesterdayVotes;

        return [

            Stat::make("Today's Votes", Number::abbreviate($todayVotes, 1))
                ->description(($trend >= 0 ? '+' : '') . $trend . ' vs yesterday')
                ->descriptionIcon(
                    $trend >= 0
                        ? 'heroicon-m-arrow-trending-up'
                        : 'heroicon-m-arrow-trending-down'
                )
                ->chart($todayChart)
                ->color($trend >= 0 ? 'success' : 'danger'),


            Stat::make('Yesterday Votes', Number::abbreviate($yesterdayVotes, 1))
                ->description('Previous day performance')
                ->chart($yesterdayChart)
                ->color('primary'),


            Stat::make('This Week Votes', Number::abbreviate($weeklyVotes, 1))
                ->description('Last 7 days')
                ->chart($weekChart)
                ->color('primary'),

            Stat::make('Votes this Month', Number::abbreviate($votesThisMonth, 1))
                ->description("Accross {$totalRestaurants} restaurants")
                ->color('primary'),

            // Stat::make('Best Rank Yesterday', $bestRank ? "#{$bestRank->rank}" : 'N/A')
            //     ->description("#{$bestRank->restaurant->name}")
            //     ->descriptionIcon('heroicon-m-trophy'),

        ];
    }
}
