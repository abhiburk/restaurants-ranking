<?php

namespace App\Filament\Resources\Contributors\Schemas;

use App\Enums\ContributorStatus;
use App\Enums\UserType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class ContributorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name', fn(Builder $query) => $query->where('role', UserType::CONTRIBUTOR))
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('city_id')
                    ->label('City')
                    ->required()
                    ->helperText('The city where this contributor is located')
                    ->searchable()
                    ->preload()
                    ->relationship('city', 'name', fn(Builder $query) => $query->where('is_active', 1)),
                Select::make('level')
                    ->relationship('contributor_level', 'name')
                    ->required()
                    ->default(1),
                Select::make('status')
                    ->options(ContributorStatus::class)
                    ->label('Status')
                    ->required(),
                Textarea::make('reason')
                    ->rows(1)
                    ->label('Reason for rejection'),
                Select::make('review_tier')
                    ->options(['standard' => 'Standard', 'fast_track' => 'Fast track', 'peer_reviewer' => 'Peer reviewer'])
                    ->default('standard')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
