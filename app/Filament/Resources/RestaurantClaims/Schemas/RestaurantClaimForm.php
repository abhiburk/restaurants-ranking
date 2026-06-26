<?php

namespace App\Filament\Resources\RestaurantClaims\Schemas;

use App\Enums\RestaurantClaimStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RestaurantClaimForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id')
                    ->label('ID')
                    ->disabled(),
                Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->disabled(),
                Select::make('restaurant_id')
                    ->label('Restaurant')
                    ->relationship('restaurant', 'name')
                    ->disabled(),
                Select::make('city_id')
                    ->label('City')
                    ->relationship('city', 'name')
                    ->disabled(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->disabled(),
                TextInput::make('phone')
                    ->tel()
                    ->disabled(),
                
                Textarea::make('notes')
                    ->label('Notes')
                    ->disabled(),
                Textarea::make('reason')
                    ->label('Admin Reason'),
                TextInput::make('document')
                    ->label('Verification Document')
                    ->columnSpanFull()
                    ->disabled(),
                // Select::make('status')
                //     ->label('Status')
                //     ->required()
                //     ->default(RestaurantClaimStatus::Pending->value)
                //     ->options(RestaurantClaimStatus::cases()),
                DateTimePicker::make('rejected_at')->visible(fn ($get) => $get('status') === RestaurantClaimStatus::REJECTED->value)->disabled(),
                DateTimePicker::make('approved_at')->visible(fn ($get) => $get('status') === RestaurantClaimStatus::APPROVED->value)->disabled(),
            ]);
    }
}
