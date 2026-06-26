<?php

namespace App\Filament\Resources\CityWaitlists\Pages;

use App\Filament\Resources\CityWaitlists\CityWaitlistResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCityWaitlists extends ListRecords
{
    protected static string $resource = CityWaitlistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
