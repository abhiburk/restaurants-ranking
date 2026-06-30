<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Restaurant;
use App\Models\Vote;
use App\Services\CityService;
use App\Services\RestaurantStatsService;
use Illuminate\Http\Request;
use Illuminate\Support\Number;
use Inertia\Inertia;

class DiscoverController extends Controller
{
    public function index(Request $request)
    {
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        $totalActiveCities = Inertia::defer(
            fn() =>
            cache()->remember('stats.active-cities', 60 * 60, fn() => City::active()->count()),
            'stats'
        );
        $totalActiveRestaurants = Inertia::defer(
            fn() =>
            cache()->remember('stats.active-restaurants', 60 * 60, fn() => Restaurant::active()->count()),
            'stats'
        );

        $totalVotesToday = Inertia::defer(
            fn() =>
            cache()->remember('stats.votes-today', 60, fn() => Number::abbreviate(Vote::whereBetween('voted_at', [$todayStart, $todayEnd])->count())),
            'stats'
        );

        $comingSoonCities = Inertia::defer(
            fn() => City::comingSoon()->select(['name', 'slug'])->limit(5)->get(['name', 'slug']),
            'right-sidebar'
        );

        $recentlyAddedRestaurants =
            Inertia::defer(
                fn() =>
                Restaurant::active()
                    ->select(['restaurants.name', 'restaurants.slug', 'restaurants.address', 'restaurants.city_id', 'banner', 'logo', 'created_at'])
                    ->with(['city:id,name,slug,state_id', 'city.state:id,name,slug'])
                    ->latest()
                    ->limit(5)
                    ->get(),
                'right-sidebar'
            );

        $activeCities = Inertia::defer(fn() => (new CityService)->listCities());

        // Most active restaurant by total votes
        $mostActiveRestaurant =
            Inertia::defer(
                fn() =>
                Restaurant::active()
                    ->select(['restaurants.name', 'restaurants.slug', 'restaurants.address', 'restaurants.city_id', 'banner', 'logo', 'views'])
                    // ->withGrowthPercentage()
                    ->with(['city:id,name,slug,state_id', 'city.state:id,name,slug'])
                    // ->orderByDesc('votes_today')
                    ->first(),
            );

        return inertia('Discover', [
            'totalActiveCities' => $totalActiveCities,
            'totalActiveRestaurants' => $totalActiveRestaurants,
            'totalVotesToday' => $totalVotesToday,
            'activeCities' => $activeCities,
            'comingSoonCities' => $comingSoonCities,
            'mostActiveRestaurant' => $mostActiveRestaurant,
            'recentlyAddedRestaurants' => $recentlyAddedRestaurants
        ]);
    }

    public function home()
    {
        return inertia('Home');
    }

    public function howItWorks()
    {
        return inertia('HowItWorks');
    }

    public function about()
    {
        return inertia('company/About');
    }

    public function howClaimRestaurantWorks()
    {
        return inertia('company/HowClaimRestaurantWorks');
    }

    public function privacyPolicy()
    {
        return inertia('company/PrivacyPolicy');
    }

    public function termsOfService()
    {
        return inertia('company/TermsOfService');
    }

    public function contactUs()
    {
        return inertia('company/ContactUs');
    }

    public function community()
    {
        return inertia('company/Community');
    }

    public function explore(Request $request)
    {
        $search = trim($request->input('search', ''));
        $perPage = (int) $request->input('per_page', 5);
        $paginatedRestaurants = Restaurant::select('restaurants.*')
            ->active()
            ->when($search, function ($q) use ($search) {
                $q->whereLike('name', "%{$search}%")->orWhere('address', 'like', "%{$search}%");
            })
            ->withGrowthPercentage() // Eager load growth_percentage scope
            ->with(['category:id,name,slug', 'city:id,name,slug', 'media'])
            ->orderByDesc('votes_today')
            ->paginate($perPage)
            ->onEachSide(1)
            ->withQueryString();

        $restaurants = RestaurantStatsService::forListing(collect($paginatedRestaurants->items()));

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

        $cities = City::active()->withCount('restaurants')->orderByDesc('restaurants_count')->limit(5)->get();

        return inertia('Explore', [
            'filters' => $request->only('search', 'per_page'),
            'restaurants' => $paginatedRestaurants,
            'cities' => $cities
        ]);
    }
}
