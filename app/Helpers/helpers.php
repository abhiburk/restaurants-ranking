<?php

use App\Enums\UserType;
use App\Models\User;

if (! function_exists('super_admin')) {
    function super_admin()
    {
        return User::where('role', UserType::SUPER_ADMIN)->first();
    }
}