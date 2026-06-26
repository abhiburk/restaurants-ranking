<?php

namespace Database\Seeders;

use App\Models\RestaurantCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RestaurantCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Fast Food', 'status' => true],
            ['name' => 'Fine Dining', 'status' => true],
            ['name' => 'Cafe', 'status' => true],
            ['name' => 'Buffet', 'status' => true],
            ['name' => 'Food Truck', 'status' => true],
            ['name' => 'Casual Dining', 'status' => true],
            ['name' => 'Pizzeria', 'slug' => 'pizzeria', 'status' => true],
            ['name' => 'Barbecue', 'slug' => 'barbecue', 'status' => true],
            ['name' => 'Street Food', 'slug' => 'street-food', 'status' => true],
        ];
        foreach ($categories as $category) {
            RestaurantCategory::updateOrCreate([
                'name' => $category['name'],
                'is_active' => $category['status'],
            ]);
        }
    }
}