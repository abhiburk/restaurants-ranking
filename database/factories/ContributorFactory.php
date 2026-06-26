<?php

namespace Database\Factories;

use App\Enums\ContributorStatus;
use App\Enums\UserType;
use App\Models\City;
use App\Models\ContributorLevel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContributorFactory extends Factory
{
    public function definition(): array
    {
        $user = User::where('role', UserType::CONTRIBUTOR)->inRandomOrder()->first();
        $superAdmin = super_admin();
        $isActive = $this->faker->randomElement([true, false]);
        return [
            'user_id' => $user->id,
            'city_id' => City::inRandomOrder()->active()->first()->id,
            'motivation' => $this->faker->sentence(),
            'contributor_level_id' => ContributorLevel::where('level', 1)->first()->id,
            'status' => $isActive ? ContributorStatus::APPROVED->value : ContributorStatus::PENDING->value,
            'is_active' => $isActive,
            'reviewed_at' => $isActive ? now() : null,
            'reviewed_by' => $isActive ? $superAdmin->id : null,
            'joined_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
