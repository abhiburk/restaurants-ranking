<?php

namespace App\Models;

use App\Enums\RestaurantClaimStatus;
use App\Enums\UserType;
use App\Notifications\User\RestaurantClaim\RestaurantClaimApprovedNotification;
use App\Notifications\User\RestaurantClaim\RestaurantClaimRejectedNotification;
use App\Services\ContributorPointsService;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class RestaurantClaim extends Model
{
    use HasUuids, SoftDeletes;

    protected $guarded = ['id'];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function updateStatus(string $status, $reason = null)
    {
        if ($this->status == $status) {
            return;
        }

        $this->reviewed_at = now();
        $contributor = $this->restaurant->contributor;

        if ($status === RestaurantClaimStatus::APPROVED->value) {
            $this->restaurant->update(['user_id' => $this->user_id]);

            $user = $this->user->fresh();

            if ($user->role == UserType::USER->value) {
                Log::info('User role:', [$user]);
                $user->update(['role' => UserType::PARTNER->value]);
                $user->syncRoles([UserType::PARTNER->value]); 
            }

            if ($contributor) {
                // Update contributor points
                (new ContributorPointsService())->onClaimApproved($contributor, $this);

                // Notify user
                $this->user->notify(new RestaurantClaimApprovedNotification($this));
            }
        } elseif ($status === RestaurantClaimStatus::REJECTED->value) {
            $this->reason = $reason;

            if ($contributor) {
                // Update contributor points
                (new ContributorPointsService())->onClaimRejected($contributor, $this);

                // Notify user
                $this->user->notify(new RestaurantClaimRejectedNotification($this));
            }
        }

        $this->status = $status;
        $this->save();
    }
}
