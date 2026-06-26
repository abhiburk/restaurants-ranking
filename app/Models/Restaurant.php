<?php

namespace App\Models;

use App\Enums\RestaurantStatus;
use App\Services\RestaurantStatsService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Restaurant extends Model implements HasMedia
{
    use HasFactory, HasSlug, HasUuids, SoftDeletes, InteractsWithMedia;

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'settings' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean',
        'amenities' => 'array',
    ];

    protected $appends = [
        'is_open',
        'banner_url',
        'logo_url',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->fit(Fit::Contain, 300, 300)
            ->nonQueued();
    }

    public function user()
    {
        return $this->hasMany(User::class);
    }

    public function setIsActiveAttribute($value)
    {
        $this->attributes['is_active'] = $value;
        $this->attributes['status'] = $value ? RestaurantStatus::ACTIVE : RestaurantStatus::INACTIVE;
    }

    public function getLogoUrlAttribute($value)
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }

        return asset('storage/misc/400x400.png');
    }

    public function getBannerUrlAttribute($value)
    {
        if ($this->banner) {
            return asset('storage/' . $this->banner);
        }

        return "https://picsum.photos/600/300";
        return "https://placehold.co/1200x500/e2e8f0/475569?text={$this->name}";

        return asset('storage/misc/600x400.png');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    protected function stats(): Attribute
    {
        return Attribute::make(
            get: fn() => new RestaurantStatsService($this)
        );
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(RestaurantCategory::class, 'category_id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function archived_votes(): HasMany
    {
        return $this->hasMany(VoteArchive::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function scopeWithVotesToday($query)
    {
        $votesTodaySub = Vote::query()
            ->select('restaurant_id', DB::raw('COUNT(*) as votes_today'))
            ->today()
            ->groupBy('restaurant_id');

        return $query
            ->leftJoinSub($votesTodaySub, 'votes_today', function ($join) {
                $join->on('votes_today.restaurant_id', '=', 'restaurants.id');
            })
            ->addSelect(
                DB::raw('IFNULL(votes_today.votes_today, 0) as votes_today')
            );
    }

    public function scopeWithVotesYesterday($query)
    {
        $votesYesterdaySub = VoteArchive::query()
            ->select('restaurant_id', DB::raw('SUM(votes) as votes_yesterday'))
            ->yesterday()
            ->groupBy('restaurant_id');

        return $query
            ->leftJoinSub($votesYesterdaySub, 'votes_yesterday', function ($join) {
                $join->on('votes_yesterday.restaurant_id', '=', 'restaurants.id');
            })
            ->addSelect(
                DB::raw('IFNULL(votes_yesterday.votes_yesterday, 0) as votes_yesterday')
            );
    }

    public function scopeWithGrowthPercentage($query)
    {
        return $query
            ->withVotesToday()
            ->withVotesYesterday()
            ->addSelect([
                DB::raw('
                CASE
                    WHEN IFNULL(votes_yesterday.votes_yesterday, 0) = 0
                    THEN 100
                    ELSE ROUND(
                        (
                            (
                                IFNULL(votes_today.votes_today, 0)
                                - votes_yesterday.votes_yesterday
                            ) / votes_yesterday.votes_yesterday
                        ) * 100
                    )
                END as growth_percentage
            '),
            ]);
    }

    public function generateQrCode()
    {
        $this->qr_code()->updateOrCreate(['restaurant_id' => $this->id], [
            'name' => $this->name,
            'url' => route('restaurants.qr.download', $this),
        ]);
    }

    public function qr_code(): HasOne
    {
        return $this->hasOne(QrCode::class);
    }

    public function getIsOpenAttribute($value)
    {
        $openHours = $this->open_hours ?? null;
        $closeHours = $this->close_hours ?? null;
        if (! $openHours || ! $closeHours) {
            return null; // Unknown
        }

        $now = now();
        $openTime = now()->setTimeFromTimeString($openHours);
        $closeTime = now()->setTimeFromTimeString($closeHours);
        if ($now->between($openTime, $closeTime)) {
            return 'Open Now';
        }

        return 'Closed';
    }

    public function restaurant_claims(): HasMany
    {
        return $this->hasMany(RestaurantClaim::class);
    }

    public function restaurant_views(): HasMany
    {
        return $this->hasMany(RestaurantView::class);
    }

    public function restaurant_submission(): HasOne
    {
        return $this->hasOne(RestaurantSubmission::class);
    }
    
    public function contributor(): BelongsTo
    {
        return $this->belongsTo(Contributor::class);
    }
}
