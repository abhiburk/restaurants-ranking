<?php

namespace App\Filament\Resources\States\Resources\Cities\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Toggle::make('is_active')
                    ->label('Active')
                    ->helperText('Indicates whether the city is active or not. If the city is not active, it will not be visible to users.')
                    ->required(),
                Toggle::make('is_live')
                    ->label('Live')
                    ->helperText('Indicates whether the city is live or not. If the city is active but not live, it will be visible to users but not accessible. This can be used for cities that are coming soon or under maintenance.')
                    ->required(),
                FileUpload::make('banner')
                    ->image()
                    ->directory('cities')
                    ->visibility('public')
                    ->label('Banner Image')
                    ->imageEditor()
                    ->helperText('Upload a banner image for the city. This image will be displayed on the city page.'),
            ]);
    }
}
