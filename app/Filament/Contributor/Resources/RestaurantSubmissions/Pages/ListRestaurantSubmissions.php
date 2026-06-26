<?php

namespace App\Filament\Contributor\Resources\RestaurantSubmissions\Pages;

use App\Filament\Contributor\Resources\RestaurantSubmissions\RestaurantSubmissionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRestaurantSubmissions extends ListRecords
{
    protected static string $resource = RestaurantSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
