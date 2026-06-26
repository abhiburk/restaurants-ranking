<?php

namespace App\Enums;

enum RestaurantStatus: string
{
    case ACTIVE = 'Active';
    case INACTIVE = 'Inactive';
    case SUSPENDED = 'Suspended';
}
