<?php

namespace App\Enums;

enum Amenities: string
{
    case Coffee = '☕ Coffee';
    case DineIn = '🍽️ Dine-In';
    case Parking = '🚗 Parking';
    case WiFi = '📶 Wi-Fi';
    case PetFriendly = '🐾 Pet Friendly';
    case LiveMusic = '🎶 Live Music';
    case Bar = '🍸 Bar';
    case OutdoorSeating = '🌿 Outdoor Seating';
    case Delivery = '🛵 Delivery';
    case VeganOptions = '🥗 Vegan Options';
    case VegetarianOptions = '🥬 Vegetarian Options';
    case KidsMenu = '🍔 Kids Menu';
    case WheelchairAccessible = '♿ Wheelchair Accessible';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $amenity) => [
                $amenity->name => $amenity->value,
            ])
            ->toArray();
    }
}