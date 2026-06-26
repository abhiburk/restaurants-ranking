<?php

namespace App\Filament\Resources\RestaurantSubmissions;

use App\Enums\RestaurantSubmissionStatus;
use App\Filament\Resources\RestaurantSubmissions\Pages\CreateRestaurantSubmission;
use App\Filament\Resources\RestaurantSubmissions\Pages\EditRestaurantSubmission;
use App\Filament\Resources\RestaurantSubmissions\Pages\ListRestaurantSubmissions;
use App\Filament\Resources\RestaurantSubmissions\Schemas\RestaurantSubmissionForm;
use App\Filament\Resources\RestaurantSubmissions\Tables\RestaurantSubmissionsTable;
use App\Models\RestaurantSubmission;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class RestaurantSubmissionResource extends Resource
{
    protected static ?string $model = RestaurantSubmission::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Plus;

    protected static ?string $recordTitleAttribute = 'Restaurant Submission';

    protected static string | UnitEnum | null $navigationGroup = 'Restaurants';

    public static function form(Schema $schema): Schema
    {
        return RestaurantSubmissionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RestaurantSubmissionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRestaurantSubmissions::route('/'),
            'create' => CreateRestaurantSubmission::route('/create'),
            'edit' => EditRestaurantSubmission::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', RestaurantSubmissionStatus::PENDING)->count();
    }
}
