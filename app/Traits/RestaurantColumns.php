<?php

namespace App\Traits;

use App\Enums\RestaurantStatus;
use Illuminate\Database\Schema\Blueprint;

trait RestaurantColumns
{
    public static function columns(Blueprint $table): void
    {
        $table->string('name');
        $table->text('description')->nullable();
        $table->string('email')->nullable();
        $table->string('phone')->nullable();
        $table->text('address')->nullable();
        $table->foreignUuid('category_id');
        $table->foreignUuid('city_id')->comment('ID of the city where the restaurant is located');
        $table->string('locality')->nullable();
        $table->string('state')->nullable();
        $table->string('country')->nullable();
        $table->string('postal_code')->nullable();
        $table->string('website_url')->nullable();
        $table->string('logo')->nullable()->comment('A logo image for the restaurant, used in listings and promotions');
        $table->string('banner')->nullable()->comment('A banner image for the restaurant, used in listings and promotions');
        $table->time('open_hours')->nullable();
        $table->time('close_hours')->nullable();
        $table->string('google_maps_url')->nullable();
        $table->string('google_reviews_url')->nullable();
        $table->float('google_rating')->nullable();
        $table->string('google_place_id')->nullable();
        $table->integer('google_reviews')->nullable();
        $table->decimal('latitude', 10, 8)->nullable();
        $table->decimal('longitude', 11, 8)->nullable();
        $table->integer('radius')->nullable()->comment('The radius of the restaurant in meters to be calculated from the latitude and longitude');
        $table->text('amenities')->nullable()->comment('A JSON array of amenities offered by the restaurant, such as Wi-Fi, outdoor seating, etc.');

        $table->index(['status']);
        
    }
}
