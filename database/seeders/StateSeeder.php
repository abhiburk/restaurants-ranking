<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = \Illuminate\Support\Facades\File::get("database/data/states.json");
        $data = json_decode($json);
        $india = Country::where('name', 'India')->first();
        foreach ($data as $state) {
            State::create([
                'name' => $state->name,
                'code' => $state->code,
                'country_id' => $india->id,
                'is_active' => $state->is_active ?? false,
            ]);
        }
    }
}
