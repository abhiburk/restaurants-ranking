<?php

namespace App\Filament\Contributor\Resources\RestaurantSubmissions\Pages;

use App\Filament\Contributor\Resources\RestaurantSubmissions\RestaurantSubmissionResource;
use App\Services\ContributorPointsService;
use Filament\Resources\Pages\CreateRecord;

class CreateRestaurantSubmission extends CreateRecord
{
    protected static string $resource = RestaurantSubmissionResource::class;

    protected function afterCreate(): void
    {
        (new ContributorPointsService())->onSubmissionCreated($this->contributor, $this->record);
    }
}
