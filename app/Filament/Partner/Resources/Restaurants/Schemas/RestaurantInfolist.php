<?php

namespace App\Filament\Partner\Resources\Restaurants\Schemas;

use App\Models\Restaurant;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class RestaurantInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('name'),
                TextEntry::make('slug'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('email')
                    ->label('Email address')
                    ->placeholder('-'),
                TextEntry::make('phone')
                    ->placeholder('-'),
                TextEntry::make('address')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('category_id'),
                TextEntry::make('city_id')
                    ->placeholder('-'),
                TextEntry::make('city')
                    ->placeholder('-'),
                TextEntry::make('state')
                    ->placeholder('-'),
                TextEntry::make('country')
                    ->placeholder('-'),
                TextEntry::make('postal_code')
                    ->placeholder('-'),
                TextEntry::make('website_url')
                    ->placeholder('-'),
                TextEntry::make('logo')
                    ->placeholder('-'),
                TextEntry::make('google_maps_url')
                    ->placeholder('-'),
                TextEntry::make('google_reviews_url')
                    ->placeholder('-'),
                TextEntry::make('google_rating')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('google_place_id')
                    ->placeholder('-'),
                TextEntry::make('google_reviews')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('latitude')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('longitude')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('is_active')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('is_default')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('user_id')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Restaurant $record): bool => $record->trashed()),
            ]);
    }
}
