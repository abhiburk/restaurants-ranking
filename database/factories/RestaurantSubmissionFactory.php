<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Contributor;
use App\Models\ContributorApplication;
use App\Models\RestaurantCategory;
use App\Models\RestaurantSubmission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RestaurantSubmissionFactory extends Factory
{
    protected $model = RestaurantSubmission::class;

    public function definition(): array
    {
        // Generate a unique restaurant name
        $name = $this->faker->unique()->company() . ' ' . $this->faker->randomElement(['Bistro', 'Restaurant', 'Kitchen', 'Eatery', 'Cafe']);

        // Generate random rating with one decimal place
        $rating = $this->faker->randomFloat(1, 3.0, 5.0);

        // Random number of reviews based on rating (higher rating = more reviews)
        $reviews = $this->faker->numberBetween(10, 500);

        $contributor = Contributor::inRandomOrder()->active()->first();

        return [
            'name' => $name,
            // 'slug' => $slug,
            'user_id' => $contributor->user?->id,
            'contributor_id' => $contributor->id,
            'description' => $this->faker->paragraphs(3, true),
            'address' => $this->faker->streetAddress(),
            'city_id' => $contributor->city_id,
            'category_id' => RestaurantCategory::where('is_active', true)->inRandomOrder()->first()->id,
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->companyEmail(),
            'website_url' => $this->faker->url(),
            'google_maps_url' => $this->generateGoogleMapsUrl($name),
            'google_place_id' => $this->faker->optional(0.7)->regexify('[A-Za-z0-9]{27}'),
            'google_rating' => $rating,
            'google_reviews' => $reviews,
            'locality' => $this->faker->city(),
            'state' => $this->faker->state(),
            'country' => $this->faker->country(),
            // set latitude and longitude within the range of Maharashtra, India
            'latitude' => $this->faker->latitude(19.5, 20.5),
            'longitude' => $this->faker->longitude(75.5, 77.5),
            // 'is_active' => $this->faker->randomElement([true, false]),
            'open_hours' => $this->faker->time('H:i'),
            'close_hours' => $this->faker->time('H:i'),
            'amenities' => $this->randomAmenity(),
        ];
    }

    private function randomAmenity(): array
    {
        $amenities = ([
            '☕ Coffee',
            '🍽️ Dine-In',
            '🚗 Parking',
            '📶 Wi-Fi',
            '🐾 Pet Friendly',
            '🎶 Live Music',
            '🍸 Bar',
            '🌿 Outdoor Seating',
            '🛵 Delivery',
            '🥗 Vegan Options',
            '🥬 Vegetarian Options',
            '🍔 Kids Menu',
            '♿ Wheelchair Accessible',
        ]);
        return $this->faker->randomElements($amenities, $this->faker->numberBetween(1, count($amenities)));
    }

    /**
     * Generate a Google Maps URL based on restaurant name
     */
    private function generateGoogleMapsUrl(string $name): string
    {
        $query = urlencode($name . ' restaurant');
        return 'https://maps.google.com/?q=' . $query;
    }

    /**
     * Configure restaurant with high rating (4.5+)
     */
    public function highRated(): static
    {
        return $this->state(fn(array $attributes) => [
            'google_rating' => $this->faker->randomFloat(1, 4.5, 5.0),
            'google_reviews' => $this->faker->numberBetween(200, 1000),
        ]);
    }

    /**
     * Configure restaurant with specific city
     */
    public function inCity(int $cityId): static
    {
        return $this->state(fn(array $attributes) => [
            'city_id' => $cityId,
        ]);
    }

    /**
     * Configure restaurant with specific category
     */
    public function inCategory(int $categoryId): static
    {
        return $this->state(fn(array $attributes) => [
            'category_id' => $categoryId,
        ]);
    }

    /**
     * Configure restaurant with specific coordinates
     */
    public function withCoordinates(float $latitude, float $longitude): static
    {
        return $this->state(fn(array $attributes) => [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
    }
}
