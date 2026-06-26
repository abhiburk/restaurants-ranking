<?php

namespace App\Filament\Contributor\Resources\RestaurantSubmissions;

use App\Filament\Contributor\Resources\RestaurantSubmissions\Pages\CreateRestaurantSubmission;
use App\Filament\Contributor\Resources\RestaurantSubmissions\Pages\EditRestaurantSubmission;
use App\Filament\Contributor\Resources\RestaurantSubmissions\Pages\ListRestaurantSubmissions;
use App\Filament\Contributor\Resources\RestaurantSubmissions\Pages\ViewRestaurantSubmission;
use App\Filament\Contributor\Resources\RestaurantSubmissions\Schemas\RestaurantSubmissionForm;
use App\Filament\Contributor\Resources\RestaurantSubmissions\Schemas\RestaurantSubmissionInfolist;
use App\Filament\Contributor\Resources\RestaurantSubmissions\Tables\RestaurantSubmissionsTable;
use App\Models\RestaurantSubmission;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RestaurantSubmissionResource extends Resource
{
    protected static ?string $model = RestaurantSubmission::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingStorefront;

    protected static ?string $recordTitleAttribute = 'Restaurant Submission';

    public static function form(Schema $schema): Schema
    {
        return RestaurantSubmissionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RestaurantSubmissionInfolist::configure($schema);
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
            'view' => ViewRestaurantSubmission::route('/{record}'),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }
}
