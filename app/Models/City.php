<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class City extends Model
{
    use HasSlug, HasUuids;

    protected $guarded = ['id'];

    protected $appends = ['banner_url'];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }

    public function scopeActive($query)
    {
        return $query->where('cities.is_active', true);
    }

    public function scopeIsLive($query)
    {
        return $query->where('cities.is_live', true);
    }

    public function scopeComingSoon($query)
    {
        return $query->where('cities.is_live', false);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function restaurants()
    {
        return $this->hasMany(Restaurant::class, 'city_id');
    }

    public function top_restaurant_today()
    {
        return $this->hasOne(Restaurant::class)
            ->join('votes', 'votes.restaurant_id', '=', 'restaurants.id')
            ->whereNull('votes.deleted_at')
            ->whereBetween('votes.voted_at', [today()->startOfDay(), today()->endOfDay()])
            ->groupBy('restaurants.id')
            ->selectRaw('restaurants.*, COUNT(votes.id) as votes_today')
            ->orderByRaw('COUNT(votes.id) DESC');
    }

    public function getBannerUrlAttribute($value)
    {
        if ($this->banner) {
            return asset('storage/' . $this->banner);
        }

        return "https://picsum.photos/600/300";

        return "https://placehold.co/1200x500/e2e8f0/475569?text={$this->name}";

        return asset('storage/misc/default-city.png');
    }

    public function city_wishlists()
    {
        return $this->hasMany(CityWaitlist::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class, 'city_id');
    }

    public function vote_archives(): HasMany
    {
        return $this->hasMany(VoteArchive::class, 'city_id');
    }

    public function scopeWithVotesToday(Builder $query): Builder
    {
        $votesTodaySub = Vote::query()
            ->today()
            ->selectRaw('city_id, COUNT(city_id) AS votes_today')
            ->groupBy('city_id');

        return $query
            ->leftJoinSub($votesTodaySub, 'votes_today', function ($join) {
                $join->on('votes_today.city_id', '=', 'cities.id');
            })
            ->addSelect(DB::raw('COALESCE(votes_today.votes_today, 0) AS votes_today'));
    }

    public function scopeWithVotesYesterday(Builder $query): Builder
    {
        $votesYesterdaySub = VoteArchive::query()
            ->selectRaw('city_id, SUM(votes) AS votes_yesterday')
            ->yesterday()
            ->groupBy('city_id');

        return $query
            ->leftJoinSub($votesYesterdaySub, 'votes_yesterday', function ($join) {
                $join->on('votes_yesterday.city_id', '=', 'cities.id');
            })
            ->addSelect(DB::raw('COALESCE(votes_yesterday.votes_yesterday, 0) AS votes_yesterday'));
    }

    public function scopeWithGrowthPercentage(Builder $query): Builder
    {
        return $query
            ->withVotesToday()
            ->withVotesYesterday()
            ->selectRaw("
                CASE
                    WHEN COALESCE(votes_yesterday.votes_yesterday, 0) = 0
                        THEN 100
                    ELSE ROUND(
                        (
                            (
                                COALESCE(votes_today.votes_today, 0)
                                - votes_yesterday.votes_yesterday
                            ) / votes_yesterday.votes_yesterday
                        ) * 100
                    )
                END AS growth_percentage
            ");
    }

    public function growth_percentage()
    {
        return ceil($this->votes_yesterday ? (($this->votes_today - $this->votes_yesterday) / $this->votes_yesterday * 100) : 0);
    }



    public function contributors(): HasMany
    {
        return $this->hasMany(Contributor::class, 'city_id');
    }
}
