<?php

namespace Database\Seeders;

use App\Models\Contributor;
use Illuminate\Database\Seeder;

class ContributorSeeder extends Seeder
{
    public function run(): void
    {
        Contributor::factory()->count(3)->create();
    }
}
