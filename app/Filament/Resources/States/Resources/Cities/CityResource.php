<?php

namespace App\Filament\Resources\States\Resources\Cities;

use App\Filament\Resources\States\Resources\Cities\Pages\CreateCity;
use App\Filament\Resources\States\Resources\Cities\Pages\EditCity;
use App\Filament\Resources\States\Resources\Cities\Pages\ViewCity;
use App\Filament\Resources\States\Resources\Cities\Schemas\CityForm;
use App\Filament\Resources\States\Resources\Cities\Schemas\CityInfolist;
use App\Filament\Resources\States\Resources\Cities\Tables\CitiesTable;
use App\Filament\Resources\States\StateResource;
use App\Models\City;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $parentResource = StateResource::class;

    protected static ?string $recordTitleAttribute = 'Cities';

    public static function form(Schema $schema): Schema
    {
        return CityForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CitiesTable::configure($table);
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
            'create' => CreateCity::route('/create'),
            'edit' => EditCity::route('/{record}/edit'),
        ];
    }
}
