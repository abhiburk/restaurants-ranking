<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get("database/data/cities.json");
        $data = json_decode($json);
        $states = State::pluck('id', 'code')->toArray();
        $activeCities = ['Delhi', 'Mumbai', 'Chennai', 'Kolkata', 'Bangalore', 'Chhatrapati Sambhajinagar', 'Pune'];
        $comingSoonCities = ['Jaipur', 'Lucknow', 'Hyderabad', 'Ahmedabad', 'Surat'];
        foreach ($data as $city) {
            City::create([
                'name' => $city->name,
                'state_id' => $states[$city->state_code],
                'is_active' => in_array($city->name, $activeCities),
                'is_live' => !in_array($city->name, $comingSoonCities),
            ]);
        }
    }
}
