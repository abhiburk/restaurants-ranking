<?php

namespace App\Filament\Contributor\Resources\RestaurantSubmissions\Pages;

use App\Filament\Contributor\Resources\RestaurantSubmissions\RestaurantSubmissionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditRestaurantSubmission extends EditRecord
{
    protected static string $resource = RestaurantSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
