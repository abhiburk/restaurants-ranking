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

        $allTimeVotesToday = Inertia::defer(
            fn() => Vote::today()->where('city_id', $city->id)->count(),
            'stats'
        );
        $waitListCount = Inertia::defer(
            fn() => $city->city_wishlists()->count() ?? 0,
            'stats'
        );

        $city = City::query()->select('cities.*')->whereId($city->id)->withGrowthPercentage()->first();
        $city->load('state:id,name,slug');
        $city->loadCount(['restaurants' => fn($q) => $q->active()]);

        $restaurantCategories = Inertia::defer(
            fn() => RestaurantCategory::active()->whereHas('restaurants', fn($q) => $q->where('city_id', $city->id)->active())->get(['id', 'name', 'slug'])
        );

        $cities = Inertia::defer(
            fn() =>
            City::active()->where('id', '!=', $city->id)->inRandomOrder()->limit(5)->get(['id', 'name', 'slug'])
        );

        $restaurants = Inertia::defer(fn() => (new RestaurantService)->listRestaurants($search, $perPage, $city, $category), 'restaurants');

        return Inertia::render('restaurant/ListRestaurant', [
            'allTimeVotesToday' => $allTimeVotesToday,
            'waitlistCount' => $waitListCount,
            'city' => $city,
            'restaurants' => $restaurants,
            'filters' => $request->only('search', 'per_page', 'category'),
            'restaurantCategories' => $restaurantCategories,
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

        $stats = Inertia::defer(function () use ($restaurant) {
            $stats = $restaurant->stats;
            return [
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
            ];
        }, 'stats');

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

        $nearbyRestaurants = Inertia::defer(
            fn() => (new RestaurantService())->findNearbyRestaurant($restaurant),
            'right-sidebar'
        );

        return Inertia::render('restaurant/ShowRestaurant', [
            'restaurant' => $restaurant,
            'voted' => $voted,
            'vote_source' => $voteSource,
            'nearbyRestaurants' => $nearbyRestaurants,
            'stats' => $stats,
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
