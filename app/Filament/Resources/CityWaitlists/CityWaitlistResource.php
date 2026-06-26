<?php

namespace App\Filament\Resources\CityWaitlists;

use App\Filament\Resources\CityWaitlists\Pages\CreateCityWaitlist;
use App\Filament\Resources\CityWaitlists\Pages\EditCityWaitlist;
use App\Filament\Resources\CityWaitlists\Pages\ListCityWaitlists;
use App\Filament\Resources\CityWaitlists\Schemas\CityWaitlistForm;
use App\Filament\Resources\CityWaitlists\Tables\CityWaitlistsTable;
use App\Models\CityWaitlist;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CityWaitlistResource extends Resource
{
    protected static ?string $model = CityWaitlist::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::QueueList;

    protected static ?string $recordTitleAttribute = 'City Waitlist';

    public static function form(Schema $schema): Schema
    {
        return CityWaitlistForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CityWaitlistsTable::configure($table);
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
            'index' => ListCityWaitlists::route('/'),
            // 'create' => CreateCityWaitlist::route('/create'),
            // 'edit' => EditCityWaitlist::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('sent_at', null)->count();
    }
}
