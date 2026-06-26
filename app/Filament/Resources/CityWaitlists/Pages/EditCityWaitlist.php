<?php

namespace App\Filament\Resources\CityWaitlists\Pages;

use App\Filament\Resources\CityWaitlists\CityWaitlistResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCityWaitlist extends EditRecord
{
    protected static string $resource = CityWaitlistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
