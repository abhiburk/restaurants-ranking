<?php

namespace Database\Seeders;

use App\Enums\UserType;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = Role::create(['name' => UserType::SUPER_ADMIN]);
        $superAdmin->syncPermissions(Permission::pluck('name')->toArray());

        $partner = Role::create(['name' => UserType::PARTNER]);
        $partner->syncPermissions([
            'create_restaurant',
            'edit_restaurant',
            'delete_restaurant',
            'view_restaurant',
            // 'review_restaurant',
        ]);

        $contributor = Role::create(['name' => UserType::CONTRIBUTOR]);
        $contributor->syncPermissions([
            'view_restaurant_submission',
            'edit_restaurant_submission',
            'delete_restaurant_submission',
            'create_restaurant_submission',
            'review_restaurant_submission',

            'view_contributors',
            'edit_contributors',
            'delete_contributors',
            'create_contributors',
        ]);

        $user = Role::create(['name' => UserType::USER]);
        $user->syncPermissions([
            'create_contributors',

            'create_restaurant_claims',
        ]);
    }
}
