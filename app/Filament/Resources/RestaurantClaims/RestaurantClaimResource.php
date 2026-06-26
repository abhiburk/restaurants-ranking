<?php

namespace App\Filament\Resources\RestaurantClaims;

use App\Enums\RestaurantClaimStatus;
use App\Filament\Resources\RestaurantClaims\Pages\CreateRestaurantClaim;
use App\Filament\Resources\RestaurantClaims\Pages\EditRestaurantClaim;
use App\Filament\Resources\RestaurantClaims\Pages\ListRestaurantClaims;
use App\Filament\Resources\RestaurantClaims\Pages\ViewRestaurantClaim;
use App\Filament\Resources\RestaurantClaims\Schemas\RestaurantClaimForm;
use App\Filament\Resources\RestaurantClaims\Schemas\RestaurantClaimInfolist;
use App\Filament\Resources\RestaurantClaims\Tables\RestaurantClaimsTable;
use App\Models\RestaurantClaim;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class RestaurantClaimResource extends Resource
{
    protected static ?string $model = RestaurantClaim::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CheckBadge;

    protected static ?string $recordTitleAttribute = 'Restaurant Claims';

    protected static string | UnitEnum | null $navigationGroup = 'Restaurants';

    public static function form(Schema $schema): Schema
    {
        return RestaurantClaimForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RestaurantClaimInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RestaurantClaimsTable::configure($table);
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
            'index' => ListRestaurantClaims::route('/'),
            'create' => CreateRestaurantClaim::route('/create'),
            'view' => ViewRestaurantClaim::route('/{record}'),
            'edit' => EditRestaurantClaim::route('/{record}/edit'),
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
        return static::getModel()::where('status', RestaurantClaimStatus::PENDING->value)->count();
    }
}
