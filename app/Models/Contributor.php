<?php

namespace App\Models;

use App\Enums\ContributorStatus;
use App\Notifications\User\Contributor\ContributorRequestApprovedNotification;
use App\Notifications\User\Contributor\ContributorRequestRejectedNotification;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contributor extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    protected $guarded = ['id'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function contributor_level(): BelongsTo
    {
        return $this->belongsTo(ContributorLevel::class);
    }

    public function restaurants_submissions(): HasMany
    {
        return $this->hasMany(RestaurantSubmission::class);
    }

    public function latest_restaurant_submission(): HasOne
    {
        return $this->hasOne(RestaurantSubmission::class)->latest();
    }

    public function updateStatus(string $status, $reason = null)
    {
        $this->reviewed_at = now();
        $this->reviewed_by = auth()->id();
        $this->status = $status;
        $this->reason = $reason;

        $status == ContributorStatus::APPROVED->value ? $this->is_active = true : $this->is_active = false;

        $this->save();

        if ($status == ContributorStatus::APPROVED->value) {
            $this->user->notify(new ContributorRequestApprovedNotification($this));
        } else {
            $this->user->notify(new ContributorRequestRejectedNotification($this));
            // $this->contributor->delete();
        }
    }

    public function contributor_logs(): HasMany
    {
        return $this->hasMany(ContributorLog::class);
    }
}
