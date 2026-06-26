<?php

namespace App\Filament\Resources\RestaurantSubmissions\Pages;

use App\Enums\RestaurantSubmissionStatus;
use App\Filament\Resources\RestaurantSubmissions\RestaurantSubmissionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListRestaurantSubmissions extends ListRecords
{
    protected static string $resource = RestaurantSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            RestaurantSubmissionStatus::PENDING->value => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', RestaurantSubmissionStatus::PENDING)),
            RestaurantSubmissionStatus::APPROVED->value => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', RestaurantSubmissionStatus::APPROVED)),
            RestaurantSubmissionStatus::REJECTED->value => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', RestaurantSubmissionStatus::REJECTED)),
        ];
    }
}
