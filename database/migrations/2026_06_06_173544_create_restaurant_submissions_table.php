<?php

use App\Enums\RestaurantSubmissionStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('restaurant_submissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->nullable()->comment('ID of the user who creates the submission');
            $table->foreignUuid('contributor_id');
            $table->foreignUuid('restaurant_id')->nullable();
            $table->string('name')->comment('Name of the restaurant');
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
            $table->enum('status', RestaurantSubmissionStatus::cases())->default(RestaurantSubmissionStatus::PENDING);
            $table->text('reason')->nullable()->comment('Reason for rejection or approval');
            $table->foreignUuid('reviewed_by')->nullable()->comment('ID of the user who reviewed the submission from the users table');
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_submissions');
    }
};
