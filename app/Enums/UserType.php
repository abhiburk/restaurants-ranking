<?php

namespace App\Enums;

enum UserType: string
{
    case USER = 'user';
    case PARTNER = 'partner';
    case SUPER_ADMIN = 'super_admin';
    case CONTRIBUTOR = 'contributor';
}
