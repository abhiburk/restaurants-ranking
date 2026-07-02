<?php

namespace App\Services;

use App\Models\City;
use App\Models\Restaurant;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class RestaurantService
{
    public function listRestaurants(?string $search = null, ?int $perPage = 5, ?City $city = null, ?string $category = null): LengthAwarePaginator
    {
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
            ->with(['category:id,name,slug', 'city:id,name,slug'])
            ->orderByDesc('votes_today')
            ->paginate($perPage);

        $restaurants = RestaurantStatsService::forListing(collect($paginatedRestaurants->items()));

        // Merge stats into the paginator's items
        $stats = $restaurants->keyBy('restaurant.id');

        return tap($paginatedRestaurants, function ($paginator) use ($stats) {
            $paginator->setCollection(
                $paginator->getCollection()->map(function ($restaurant) use ($stats) {
                    $restaurant->votes_today = $stats[$restaurant->id]['votes_today'] ?? 0;

                    return $restaurant;
                })
            );
        });
    }

    /**
     * Find nearby restaurants for a given restaurant.
     * 
     * @param Restaurant $restaurant
     * @param int $distanceRange Distance range in kilometers   
     * @return Collection
     */
    public function findNearbyRestaurant(Restaurant $restaurant, int $distanceRange = 50): Collection
    {
        $latitude = $restaurant->latitude;
        $longitude = $restaurant->longitude;
        $lngRange = $distanceRange / (111 * cos(deg2rad($latitude)));

        $latRange = $distanceRange / 111;

        $nearbyRestaurants = Restaurant::query()
            ->active()
            ->where('city_id', $restaurant->city_id)
            ->whereKeyNot($restaurant->id)
            ->with(['city:id,name,slug,state_id', 'city.state:id,name,slug'])
            ->whereBetween('latitude', [$latitude - $latRange, $latitude + $latRange])
            ->whereBetween('longitude', [$longitude - $lngRange, $longitude + $lngRange])
            ->select('*')
            ->selectRaw("
                (
                    6371 * acos(
                        cos(radians(?))
                        * cos(radians(latitude))
                        * cos(radians(longitude) - radians(?))
                        + sin(radians(?))
                        * sin(radians(latitude))
                    )
                ) AS distance
            ", [$latitude, $longitude, $latitude])
            ->having('distance', '<=', $distanceRange)
            ->orderBy('distance')
            ->limit(5)
            ->get();

        return $nearbyRestaurants;
    }
}
