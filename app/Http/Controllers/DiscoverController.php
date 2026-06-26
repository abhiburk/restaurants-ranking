<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Restaurant;
use App\Models\Vote;
use App\Services\CityService;
use Illuminate\Http\Request;
use Illuminate\Support\Number;

class DiscoverController extends Controller
{
    public function index(Request $request)
    {
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        // Simple counts
        $totalActiveCities = cache()->remember(
            'stats.active-cities',
            60 * 60, // 1 hour
            fn() => City::active()->count()
        );

        $totalActiveRestaurants = cache()->remember(
            'stats.active-restaurants',
            60 * 60, // 1 hour
            fn() => Restaurant::active()->count()
        );

        $totalVotesToday = cache()->remember(
            'stats.votes-today',
            60, // 1 minute
            fn() => Vote::whereBetween('voted_at', [$todayStart, $todayEnd])->count()
        );

        $comingSoonCities = cache()->remember(
            'coming-soon-cities',
            now()->addHours(12),
            fn() => City::comingSoon()->select(['name', 'slug'])->limit(5)->get(['name', 'slug'])->toArray()
        );

        $activeCities = (new CityService)->listCities();

        $recentlyAddedRestaurant = Restaurant::active()
            ->select(['restaurants.name', 'restaurants.slug', 'restaurants.address', 'restaurants.city_id', 'banner', 'logo', 'created_at'])
            ->with(['city:id,name,slug,state_id', 'city.state:id,name,slug'])
            ->latest()
            ->limit(5)
            ->get();

        // Most active restaurant by total votes
        $mostActiveRestaurant = Restaurant::active()
            ->select(['restaurants.name', 'restaurants.slug', 'restaurants.address', 'restaurants.city_id', 'banner', 'logo', 'views'])
            ->withGrowthPercentage()
            ->with(['city:id,name,slug,state_id', 'city.state:id,name,slug'])
            ->orderByDesc('votes_today')
            ->first();

        return inertia('Discover', [
            'totalActiveCities' => $totalActiveCities,
            'totalActiveRestaurants' => $totalActiveRestaurants,
            'totalVotesToday' => Number::abbreviate($totalVotesToday),
            'activeCities' => $activeCities,
            'comingSoonCities' => collect($comingSoonCities),
            'mostActiveRestaurant' => $mostActiveRestaurant,
            'recentlyAddedRestaurant' => $recentlyAddedRestaurant
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
}
