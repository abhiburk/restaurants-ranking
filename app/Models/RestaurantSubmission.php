<?php

namespace App\Models;

use App\Enums\RestaurantStatus;
use App\Enums\RestaurantSubmissionStatus;
use App\Notifications\User\RestaurantSubmission\RestaurantSubmissionApprovedNotification;
use App\Notifications\User\RestaurantSubmission\RestaurantSubmissionRejectedNotification;
use App\Services\ContributorPointsService;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class RestaurantSubmission extends Model implements HasMedia
{
    use HasFactory, HasUuids, InteractsWithMedia, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'settings' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean',
        'amenities' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function contributor()
    {
        return $this->belongsTo(Contributor::class);
    }

    public function reviewed_by()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(RestaurantCategory::class, 'category_id');
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function updateStatus(string $status, $reason = null)
    {
        if ($this->status == $status) {
            return;
        }

        $this->reviewed_at = now();
        $this->reviewed_by = auth()->id();
        $this->status = $status;
        $this->reason = $reason;
        $this->save();

        if ($status == RestaurantSubmissionStatus::APPROVED->value) {
            $restaurant = $this->createRestaurant();
            $this->restaurant_id = $restaurant->id;
            $this->save();

            (new ContributorPointsService())->onSubmissionApproved($this->contributor, $this);

            $this->user->notify(new RestaurantSubmissionApprovedNotification($this));
        } else {

            (new ContributorPointsService())->onSubmissionRejected($this->contributor, $this, $reason);

            $this->user->notify(new RestaurantSubmissionRejectedNotification($this));

            if ($this->restaurant) {
                $this->restaurant->update([
                    'is_active' => false,
                ]);
            }
            // $this->delete();
        }
    }

    public function createRestaurant()
    {
        $restaurant = new Restaurant;
        $restaurant->fill([
            'name' => $this->name,
            'description' => $this->description,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'category_id' => $this->category_id,
            'city_id' => $this->city_id,
            'locality' => $this->locality,
            'state' => $this->state,
            'country' => $this->country,
            'postal_code' => $this->postal_code,
            'website_url' => $this->website_url,
            'logo' => $this->logo,
            'banner' => $this->banner,
            'open_hours' => $this->open_hours,
            'close_hours' => $this->close_hours,
            'google_maps_url' => $this->google_maps_url,
            'google_reviews_url' => $this->google_reviews_url,
            'google_place_id' => $this->google_place_id,
            'google_rating' => $this->google_rating,
            'google_reviews' => $this->google_reviews,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'radius' => $this->radius,
            'amenities' => $this->amenities,
            'user_id' => null, // when claimed by restaurant owner
            'contributor_id' => $this->contributor_id,
            'slug' => $this->slug,
            'is_active' => true,
            'status' => RestaurantStatus::ACTIVE->value,
        ]);
        $restaurant->save();

        $media = $this->media;

        foreach ($media as $m) {
            $replicate = $m->replicate();
            $replicate->uuid = Str::uuid();
            $replicate->model_type = get_class($restaurant);
            $replicate->model_id = $restaurant->id;
            $replicate->save();
        }

        return $restaurant;
    }
}
