<?php

namespace App\Filament\Contributor\Resources\RestaurantSubmissions\Pages;

use App\Filament\Contributor\Resources\RestaurantSubmissions\RestaurantSubmissionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRestaurantSubmission extends ViewRecord
{
    protected static string $resource = RestaurantSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
