<?php

namespace App\Services;

use App\Models\Restaurant;
use Illuminate\Support\Collection;

class RestaurantService
{
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
