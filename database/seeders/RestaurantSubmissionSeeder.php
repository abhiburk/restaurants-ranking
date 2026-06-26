<?php

namespace Database\Seeders;

use App\Models\RestaurantSubmission;
use Illuminate\Database\Seeder;

class RestaurantSubmissionSeeder extends Seeder
{
    public function run(): void
    {
        RestaurantSubmission::factory()->count(5)->create();
    }
}
