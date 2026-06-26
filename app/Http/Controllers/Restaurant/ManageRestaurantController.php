<?php

namespace App\Http\Controllers\Restaurant;

use App\Enums\RestaurantStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Restaurant\StoreRestaurantRequest;
use App\Http\Requests\Restaurant\UpdateRestaurantRequest;
use App\Models\City;
use App\Models\Country;
use App\Models\Restaurant;
use App\Models\RestaurantCategory;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ManageRestaurantController extends Controller
{
    public function index(Request $request, ?City $city = null)
    {
        $search = $request->string('search');
        $perPage = (int) $request->get('per_page', 10);
        $restaurants = Restaurant::query()
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('city', 'like', "%{$search}%")
                ->orWhere('address', 'like', "%{$search}%")
            )
            ->when($city, function($q) use($city){
                $q->where('city_id', $city->id);
            })
            ->latest()
            ->paginate($perPage)
            ->onEachSide(1)
            ->withQueryString();

        return Inertia::render('restaurant/manage/ListRestaurant', [
            'restaurants' => $restaurants,
            'filters' => $request->only('search'),
        ]);
    }

    public function listRestaurantsByCity(Request $request, City $city)
    {
        return $this->index($request, $city);
    }

    public function search()
    {
        return Inertia::render('restaurant/manage/SearchRestaurant');
    }

    public function create(Request $request)
    {
        $india = Country::first();
        $maharashtra = State::where('country_id', $india?->id)->where('name', 'Maharashtra')->first();
        return Inertia::render('restaurant/manage/CreateRestaurant', [
            'restaurant' => $this->prepareRestaurantRequest($request->place ?? []),
            'cities' => $maharashtra?->cities()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'categories' => RestaurantCategory::where('status', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function destroy(Restaurant $restaurant)
    {
        $restaurant->delete();

        Inertia::flash([
            'type' => 'success',
            'message' => 'Restaurant deleted successfully!',
        ]);

        return to_route('restaurant.index');
    }

    public function store(StoreRestaurantRequest $request)
    {
        $restaurant = $request->user()->restaurants()->create($request->only([
            'name',
            'description',
            'email',
            'phone',
            'address',
            'category_id',
            'city_id',
            'city',
            'state',
            'country',
            'postal_code',
            'website_url',
            'logo',
            'google_maps_url',
            'google_rating',
            'google_place_id',
            'google_reviews',
            'google_reviews_url',
            'latitude',
            'longitude',
            'is_active',
            'is_default',
        ]));

        Inertia::flash([
            'type' => 'success',
            'message' => 'Restaurant created successfully!',
        ]);

        return to_route('manage.restaurant.edit', $restaurant->id);
    }

    public function edit(Restaurant $restaurant, Request $request)
    {
        $india = Country::first();
        $maharashtra = State::where('country_id', $india?->id)->where('name', 'Maharashtra')->first();
        return Inertia::render('restaurant/manage/EditRestaurant', [
            'restaurant' => $restaurant,
            'cities' => $maharashtra?->cities()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'categories' => RestaurantCategory::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function show(Restaurant $restaurant, Request $request)
    {
        return Inertia::render('restaurant/manage/ShowRestaurant', [
            'restaurant' => $restaurant,
        ]);
    }

    public function update(Restaurant $restaurant, UpdateRestaurantRequest $request)
    {
        // If the restaurant is being set as default, unset the current default restaurant
        if ($restaurant->is_default) $request->user()->restaurants()->update(['is_default' => false]);

        $file = $request->file('logo');
        if ($file) {
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('restaurants', $fileName, 'public');
            $request->merge(['logo' =>$path]);
        }

        $restaurant->update($request->only([
            'name',
            'description',
            'email',
            'phone',
            'address',
            'category_id',
            'city_id',
            'city',
            'state',
            'country',
            'postal_code',
            'website_url',
            'google_maps_url',
            'google_rating',
            'google_place_id',
            'google_reviews',
            'google_reviews_url',
            'latitude',
            'longitude',
            'is_active',
            'is_default',
        ]) + ($file ? ['logo' => $path] : []));

        Inertia::flash([
            'type' => 'success',
            'message' => 'Restaurant updated successfully!',
        ]);

        return to_route('manage.restaurant.edit', $restaurant->id);
    }

    public function updateStatus(Restaurant $restaurant)
    {
        $restaurant->update(['is_active' => !$restaurant->is_active]);

        Inertia::flash([
            'type' => 'success',
            'message' => 'Restaurant status updated successfully!',
        ]);

        return back();
    }

    public function removeLogo(Restaurant $restaurant)
    {
        Storage::disk('public')->delete($restaurant->logo);
        $restaurant->update(['logo' => null]);

        Inertia::flash([
            'type' => 'success',
            'message' => 'Restaurant logo removed successfully!',
        ]);

        return to_route('manage.restaurant.edit', $restaurant->id);
    }

    private function prepareRestaurantRequest(array $place): array
    {
        return [
            'name' => $place['name'] ?? null,
            'slug' => Str::slug($place['name'] ?? ''),
            'description' => $place['description'] ?? null,
            'email' => $place['email'] ?? null,
            'phone' => $place['phone'] ?? null,
            'address' => $place['address'] ?? null,
            'city' => $place['city'] ?? null,
            'state' => $place['state'] ?? null,
            'country' => $place['country'] ?? null,
            'postal_code' => $place['postal_code'] ?? null,
            'website_url' => $place['website'] ?? null,
            'google_maps_url' => $place['google_maps_url'] ?? null,
            'google_rating' => $place['rating'] ?? null,
            'google_place_id' => $place['place_id'] ?? null,
            'google_reviews' => $place['reviews'] ?? null,
            'google_reviews_url' => isset($place['place_id']) ? "https://search.google.com/local/writereview?placeid={$place['place_id']}" : null,
            'latitude' => $place['lat'] ?? null,
            'longitude' => $place['lng'] ?? null,
            'hours' => $place['hours'] ?? null,
        ];
    }

    public function showRestaurantsByCity(City $city, Restaurant $restaurant, Request $request)
    {
        return Inertia::render('restaurant/manage/ShowRestaurant', [
            'restaurant' => $restaurant,
        ]);
    }
}
