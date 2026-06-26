<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [

            // Restaurant Management (admin, partner)
            'create_restaurant',
            'edit_restaurant',
            'delete_restaurant',
            'view_restaurant',
            'review_restaurant',

            // User Management (admin)
            'view_users',
            'edit_users',
            'delete_users',
            'create_users',

            // Restaurant Submission (admin, contributor)
            'view_restaurant_submission',
            'edit_restaurant_submission',
            'delete_restaurant_submission',
            'create_restaurant_submission',
            'review_restaurant_submission',

            // Contributor Management (admin, user, contributor)
            'view_contributors',
            'edit_contributors',
            'delete_contributors',
            'create_contributors',
            'review_contributors',

            // Category Management (admin)
            'view_restaurant_categories',
            'edit_restaurant_categories',
            'delete_restaurant_categories',
            'create_restaurant_categories',

            // Restaurant Claims (admin)
            'view_restaurant_claims',
            'edit_restaurant_claims',
            'delete_restaurant_claims',
            'create_restaurant_claims',
            'review_restaurant_claims',

            // Waitlist Management (admin)
            'view_city_waitlist',
            'edit_city_waitlist',
            'delete_city_waitlist',
            'create_city_waitlist',
            'review_city_waitlist',

            // State Management (admin)
            'view_states',
            'edit_states',
            'delete_states',
            'create_states',

            // City Management (admin)
            'view_cities',
            'edit_cities',
            'delete_cities',
            'create_cities',

        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
