<?php

namespace App\Services;

use App\Models\City;
use Illuminate\Pagination\LengthAwarePaginator;

class CityService
{
    public function listCities(string $search = '', int $perPage = 5): LengthAwarePaginator
    {
        $search = trim($search);
        $perPage = (int) $perPage;

        $cities = City::query()
            ->select(['cities.name', 'cities.slug', 'cities.state_id'])
            ->active()
            ->with(['state:id,name,slug'])
            ->withCount(['restaurants' => function ($q) {
                $q->active();
            }])
            ->withGrowthPercentage()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('cities.name', 'LIKE', "%{$search}%")
                        ->orWhereHas('state', function ($stateQuery) use ($search) {
                            $stateQuery->where('name', 'LIKE', "%{$search}%");
                        });
                });
            })
            ->orderByDesc('growth_percentage')
            ->paginate($perPage)
            ->onEachSide(1)
            ->withQueryString();

        return $cities;
    }
}
