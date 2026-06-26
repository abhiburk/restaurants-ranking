<?php

namespace App\Enums;

enum RestaurantClaimStatus: string
{
    case PENDING = 'Pending';
    case APPROVED = 'Approved';
    case REJECTED = 'Rejected';
}
