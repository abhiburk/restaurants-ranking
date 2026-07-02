<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Vote;
use App\Services\CityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));
        $perPage = (int) $request->input('per_page', 5);

        // Cache because this section changes rarely
        $comingSoonCities = Inertia::defer(
            fn() =>
            cache()->remember(
                'coming-soon-cities',
                now()->addHours(12),
                fn() => City::comingSoon()->select(['name', 'slug'])->limit(5)->get(['name', 'slug'])->toArray()
            )
        );

        $cities = Inertia::defer(fn() => (new CityService)->listCities($search, $perPage));

        return inertia('ListCities', [
            'filters' => $request->only('search', 'per_page'),
            'activeCities' => $cities,
            'comingSoonCities' => $comingSoonCities,
        ]);
    }

    public function comingSoonCities(Request $request)
    {
        $search = $request->input('search');
        $comingSoonCities = Inertia::defer(function() use($search){
            return City::comingSoon()
            ->with(['state:id,name,slug'])
            ->where(function ($q) use ($search) {
                if ($search) {
                    $q->whereLike('name', "%{$search}%")
                        ->orWhereHas('state', function ($query) use ($search) {
                            $query->whereLike('name', "%{$search}%");
                        });
                }
            })
            ->withCount('city_wishlists')->limit(5)->get();
        });

        return inertia('ListComingSoonCities', [
            'comingSoonCities' => $comingSoonCities,
            'filters' => $request->only('search'),
        ]);
    }

    public function storeWishlist(City $city, Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
        ]);

        $email = $request->input('email');
        $name = $request->input('name');

        // Check if already in wishlist
        $exists = $city->city_wishlists()->where('email', $email)->exists();

        if ($exists) {
            inertia()->flash([
                'type' => 'success',
                'message' => 'You are already on the wishlist! We will notify you when we launch in this city.',
            ]);

            return back();
        }

        $city->city_wishlists()->create([
            'user_id' => $request->user()?->id,
            'name' => $name,
            'email' => $email,
        ]);

        inertia()->flash([
            'type' => 'success',
            'message' => 'Added to wishlist! We will notify you when we launch in this city.',
        ]);

        return back();
    }
}
