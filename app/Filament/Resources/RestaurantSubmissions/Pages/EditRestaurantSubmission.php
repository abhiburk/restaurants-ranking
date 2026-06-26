<?php

namespace App\Filament\Resources\RestaurantSubmissions\Pages;

use App\Filament\Resources\RestaurantSubmissions\RestaurantSubmissionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditRestaurantSubmission extends EditRecord
{
    protected static string $resource = RestaurantSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
