<?php

namespace App\Http\Controllers;

use App\Enums\RestaurantSubmissionStatus;
use App\Http\Requests\CreateCommunityRequest;
use App\Models\City;
use App\Models\Contributor;
use App\Models\ContributorLevel;
use App\Notifications\Admin\ContributorJoinedAdminNotification;
use App\Notifications\User\Contributor\ContributorJoinedNotification;
use App\Services\ContributorPointsService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ContributorController extends Controller
{
    public function index(Request $request)
    {
        $joinedCommunities = $request->user()
            ->contributors()
            ->with(['city:id,name'])
            ->withCount(['restaurants_submissions'])
            ->withCount(['restaurants_submissions as restaurants_submissions_approved_count' => function ($query) {
                $query->where('status', RestaurantSubmissionStatus::APPROVED);
            }])
            ->orderBy('restaurants_submissions_count', 'desc')
            ->with(['latest_restaurant_submission'])
            ->get();
        return inertia('settings/ListContributor', [
            'joinedCommunities' => $joinedCommunities
        ]);
    }

    public function create()
    {
        $activeCities = City::active()->get(['id', 'name', 'slug']);

        return inertia('community/JoinCommunity', [
            'activeCities' => $activeCities,
        ]);
    }
    public function store(CreateCommunityRequest $request)
    {
        $user = $request->user();

        if ($user->contributors()->where('city_id', $request->city_id)->exists()) {
            inertia()->flash([
                'type' => 'error',
                'message' => 'You have already joined the selected city.',
            ]);

            return redirect()->back();
        }

        $contributor = $user->contributors()->create([
            'motivation' => $request->motivation,
            'city_id' => $request->city_id,
        ]);

        // Send notification to the contributor
        $user->notify(new ContributorJoinedNotification($contributor));

        // Send notification to the super admin
        super_admin()->notify(new ContributorJoinedAdminNotification($contributor));

        inertia()->flash([
            'type' => 'success',
            'message' => 'Your request has been submitted successfully! We will get back to you soon.',
        ]);

        return to_route('settings.contributors.index');
    }

    public function show(Contributor $contributor)
    {
        abort_if($contributor->user_id != auth()->id(), Response::HTTP_FORBIDDEN);
    
        $contributorPointsService = app(ContributorPointsService::class);
        
        $contributor->load(['city:id,name,slug', 'contributor_level:id,name,level', 'contributor_logs' => function ($query) {
            $query->with('loggable');
        }]);
        $pointsThisMonth = $contributorPointsService->getMonthlyPoints($contributor);
        $maxLevel = ContributorLevel::max('level');
        $progress = $contributorPointsService->getLevelProgress($contributor);

        return inertia('settings/ShowContributor', [
            'contributor' => $contributor,
            'pointsThisMonth' => $pointsThisMonth,
            'maxLevel' => $maxLevel,
            'progress' => $progress
        ]);
    }

    public function destroy(Contributor $contributor)
    {
        $contributor->delete();

        inertia()->flash([
            'type' => 'success',
            'message' => 'You have successfully left the community.',
        ]);

        return redirect()->back();
    }
}
