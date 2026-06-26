<?php

namespace App\Filament\Partner\Resources\Restaurants\Pages;

use App\Filament\Partner\Resources\Restaurants\RestaurantResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRestaurant extends ViewRecord
{
    protected static string $resource = RestaurantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
