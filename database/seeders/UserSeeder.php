<?php

namespace Database\Seeders;

use App\Enums\UserType;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('Password@123'),
            'role' => UserType::SUPER_ADMIN,
        ]);
        $user->assignRole($user->role);

        $user = User::factory()->create([
            'name' => 'My Partner',
            'email' => 'partner@example.com',
            'password' => bcrypt('Password@123'),
            'role' => UserType::PARTNER,
        ]);
        $user->assignRole($user->role);

        $user = User::factory()->create([
            'name' => 'Abhishek Burkule',
            'email' => 'abhiburk@example.com',
            'password' => bcrypt('Password@123'),
            'role' => UserType::USER,
        ]);
        $user->assignRole($user->role);

        $user = User::factory()->create([
            'name' => 'My Contributor',
            'email' => 'contributor@example.com',
            'password' => bcrypt('Password@123'),
            'role' => UserType::CONTRIBUTOR,
        ]);
        $user->assignRole($user->role);
    }
}
