<?php

namespace App\Filament\Resources\States\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class StateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->disabled()
                    ->required(),
                TextInput::make('code')
                    ->required(),
                Select::make('country_id')
                    ->relationship('country', 'name')
                    ->label('Country')
                    ->required(),
                Toggle::make('is_active')
                    ->label('Active')
                    ->helperText('Indicates whether the state is active or not.')
                    ->required(),
            ]);
    }
}
