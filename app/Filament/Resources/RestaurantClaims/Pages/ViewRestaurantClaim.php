<?php

namespace App\Filament\Resources\RestaurantClaims\Pages;

use App\Filament\Resources\RestaurantClaims\RestaurantClaimResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRestaurantClaim extends ViewRecord
{
    protected static string $resource = RestaurantClaimResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
