<?php

namespace App\Enums;

enum RestaurantSubmissionStatus: string
{
    case PENDING = 'Pending';
    case UNDER_REVIEW = 'Under Review';
    case APPROVED = 'Approved';
    case REJECTED = 'Rejected';
    case DUPLICATE = 'Duplicate';

    public static function all(): array
    {
        return [
            self::PENDING->value,
            self::UNDER_REVIEW->value,
            self::APPROVED->value,
            self::REJECTED->value,
            self::DUPLICATE->value,
        ];
    }
}
