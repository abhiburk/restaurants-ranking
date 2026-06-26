<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRestaurantClaimRequest;
use App\Models\City;
use App\Models\QrCode;
use App\Models\Restaurant;
use App\Models\RestaurantCategory;
use App\Models\RestaurantClaim;
use App\Models\Vote;
use App\Notifications\Admin\NewRestaurantClaimNotification;
use App\Notifications\User\RestaurantClaim\RestaurantClaimSubmittedNotification;
use App\Services\RestaurantService;
use App\Services\RestaurantStatsService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use tbQuar\Facades\Quar;

class RestaurantController extends Controller
{
    public function index(Request $request, ?City $city = null)
    {
        $search = $request->input('search');
        $perPage = (int) $request->input('per_page', 5);
        $category = $request->input('category');
        $today = now()->toDateString();

        $city = City::query()->select('cities.*')->whereId($city->id)->withGrowthPercentage()->first();
        $city->load('state:id,name,slug');
        $city->loadCount(['restaurants' => fn($q) => $q->active()]);

        $restaurantCategories = RestaurantCategory::active()->limit(5)->get(['id', 'name', 'slug']);

        $allTimeVotesToday = Vote::whereDate('voted_at', $today)->where('city_id', $city->id)->count();

        $cities = City::active()->where('id', '!=', $city->id)->inRandomOrder()->limit(5)->get(['id', 'name', 'slug']);

        $paginatedRestaurants = Restaurant::select('restaurants.*')
            ->active()
            ->when($search, function ($q) use ($search) {
                $q->whereLike('name', "%{$search}%")->orWhere('address', 'like', "%{$search}%");
            })
            ->when($city, function ($q) use ($city) {
                $q->where('city_id', $city->id);
            })
            ->when($category, function ($q) use ($category) {
                $q->whereHas('category', function ($q) use ($category) {
                    $q->where('slug', $category);
                });
            })
            ->withGrowthPercentage() // Eager load growth_percentage scope
            ->with(['category:id,name,slug'])
            ->orderByDesc('votes_today')
            ->paginate($perPage)
            ->onEachSide(1)
            ->withQueryString();

        $restaurants = RestaurantStatsService::forListing(collect($paginatedRestaurants->items()));

        // Merge stats into the paginator's items
        $paginatedRestaurants->getCollection()->transform(function ($restaurant) use ($restaurants) {
            $stat = $restaurants->firstWhere('restaurant.id', $restaurant->id);

            return array_merge(
                $restaurant->toArray(),
                [
                    'votes_today' => $stat['votes_today'] ?? 0,
                    'votes_yesterday' => $stat['votes_yesterday'] ?? 0,
                    'rank' => $stat['rank'] ?? 0,
                    'streak' => $stat['streak'] ?? 0,
                    'rank_movement' => $stat['rank_movement'] ?? 0,
                    'is_trending' => $stat['is_trending'] ?? false,
                    'rank_change' => $stat['rank_change'] ?? 0,
                    'last_two_hours' => $stat['last_two_hours'] ?? 0,
                    'prev_two_hours' => $stat['prev_two_hours'] ?? 0,
                ]
            );
        });

        return Inertia::render('restaurant/ListRestaurant', [
            'city' => $city,
            'allTimeVotesToday' => $allTimeVotesToday,
            'restaurants' => $paginatedRestaurants,
            'filters' => $request->only('search', 'per_page', 'category'),
            'restaurantCategories' => $restaurantCategories,
            'waitlistCount' => $city->city_wishlists()->count() ?? 0,
            'cities' => $cities,
        ]);
    }

    public function show(City $city, Restaurant $restaurant, Request $request)
    {
        $votedAt = now();
        $visitorId = $request->cookie('visitor_id');
        $userId = $request->user()?->id;
        $voteSource = $request->qrCode?->id ? 'qr' : 'url';
        $ip = $request->ip();

        $restaurant = Restaurant::query()->select('restaurants.*')->whereId($restaurant->id)->withGrowthPercentage()->first();
        $restaurant->load(['category:id,name,slug', 'city:id,name,slug,state_id', 'city.state:id,name,slug']);
        $restaurant->load(['media' => function ($q) {
            $q->limit(5);
        }]);
        $stats = $restaurant->stats;

        $voted = null;
        if ($visitorId || $userId) {
            $voted = Vote::where('restaurant_id', $restaurant->id)
                ->when($visitorId && empty($userId), function ($q) use ($visitorId) {
                    $q->where('visitor_id', $visitorId);
                })
                ->when($userId, function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                ->whereDate('voted_at', $votedAt->toDateString())
                ->latest()
                ->first();
        }

        // Increment views in the background
        cache()->remember(
            "restaurant-view:{$restaurant->id}:{$ip}",
            now()->addMinutes(5),
            fn() => $restaurant->restaurant_views()->create([])
        );

        $nearbyRestaurants = (new RestaurantService())->findNearbyRestaurant($restaurant);

        return Inertia::render('restaurant/ShowRestaurant', [
            'restaurant' => $restaurant,
            'voted' => $voted,
            'vote_source' => $voteSource,
            'nearbyRestaurants' => $nearbyRestaurants,
            'stats' => [
                'votes_today' => $stats->votesToday(),
                'votes_yesterday' => $stats->votesYesterday(),
                'rank' => $stats->rank(),
                'total_ranked' => $stats->totalRanked(),
                'rank_change' => $stats->rankChange(),
                'votes_last_hour' => $stats->votesLastHour(),
                'hourly_change' => $stats->hourlyChange(),
                'all_time' => $stats->allTime(),
                'is_trending' => $stats->isTrendingToday(),
                'streak' => $stats->currentStreak(),       // 0 = no streak
                'previous_rank' => $stats->previousRank(),        // null = new entry
                'rank_movement_label' => $stats->rankMovementLabel(),   // "Up from #3"
                'chart' => $stats->votingHistoryInDays(),
            ],
        ]);
    }

    public function createClaim(Request $request, Restaurant $restaurant)
    {
        abort_if($restaurant->user_id, Response::HTTP_FORBIDDEN, 'This restaurant has already been claimed.');

        $restaurant->load('city:id,name,slug');
        return Inertia::render('restaurant/RestaurantClaim', [
            'restaurant' => $restaurant,
        ]);
    }

    public function storeClaim(CreateRestaurantClaimRequest $request, Restaurant $restaurant)
    {
        $city = $restaurant->city;
        $document = $request->file('document');

        $claim = $restaurant->restaurant_claims()->create([
            'user_id' => $request->user()?->id,
            'city_id' => $city->id,
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'notes' => $request->input('notes'),
            'document' => $document->store('claims', 'public'),
        ]);

        inertia()->flash([
            'type' => 'success',
            'message' => 'Your claim has been submitted.',
        ]);

        $request->user()->notify(new RestaurantClaimSubmittedNotification($claim));

        super_admin()->notify(new NewRestaurantClaimNotification($claim));

        return redirect()->route('restaurants.show', [$city->slug, $restaurant->slug]);
    }

    public function photos(Request $request, City $city, Restaurant $restaurant)
    {
        // $restaurant = Restaurant::query()->select('restaurants.*')->whereId($restaurant->id)->withGrowthPercentage()->first();
        $restaurant->load(['city:id,name,slug,state_id', 'media']);
        return Inertia::render('restaurant/RestaurantGallary', [
            'restaurant' => $restaurant,
        ]);
    }

    public function showWithQr(QrCode $qrCode, Request $request)
    {
        $qrCode->update(['total_scans' => $qrCode->total_scans + 1]);
        $restaurant = $qrCode->restaurant;

        return $this->show($restaurant->city, $restaurant, $request);
    }

    public function download(Restaurant $restaurant)
    {
        if (! $restaurant->qr_code) {
            abort(404, 'QR Code not generated');
        }

        $url = route('restaurants.show', [$restaurant->qr_code->id]);
        $qrCode = Quar::format('png')
            ->size(400)
            ->generate($url);

        return response($qrCode)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="' . $restaurant->slug . '-qr.png"');
    }
}
