<?php

namespace App\Filament\Resources\Contributors\Pages;

use App\Enums\ContributorStatus;
use App\Filament\Resources\Contributors\ContributorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListContributors extends ListRecords
{
    protected static string $resource = ContributorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            ContributorStatus::PENDING->value => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', ContributorStatus::PENDING)),
            ContributorStatus::APPROVED->value => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', ContributorStatus::APPROVED)),
            ContributorStatus::REJECTED->value => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', ContributorStatus::REJECTED)),
        ];
    }
}
