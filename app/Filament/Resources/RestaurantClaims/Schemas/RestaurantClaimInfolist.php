<?php

namespace App\Filament\Resources\RestaurantClaims\Schemas;

use App\Enums\RestaurantClaimStatus;
use App\Models\RestaurantClaim;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RestaurantClaimInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Claimant Information')
                    ->schema([
                        TextEntry::make('user.name')->label('User Name'),
                        TextEntry::make('user.email')->label('User Email'),
                        TextEntry::make('phone')->label('User Phone'),
                        TextEntry::make('notes')->label('Claim Notes'),
                    ]),
                Section::make('Restaurant Information')
                    ->schema([
                        TextEntry::make('restaurant.name')->label('Restaurant Name'),
                        TextEntry::make('city.name')->label('City'),
                        TextEntry::make('city.state.name')->label('State'),
                    ]),
                Section::make('Claim Information')
                    ->schema([
                        TextEntry::make('reason')->label('Admin Reason'),
                        TextEntry::make('document')
                            ->label('Verification Document')
                            ->color('primary')
                            ->icon('heroicon-o-document-text')
                            ->formatStateUsing(function (RestaurantClaim $record) {
                                return $record->document ? 'View Document' : '-';
                            })
                            ->openUrlInNewTab()
                            ->url(fn (RestaurantClaim $record) => asset('storage/'.$record->document)),
                        TextEntry::make('status')->color(function (RestaurantClaim $record) {
                            return match ($record->status) {
                                RestaurantClaimStatus::PENDING->value => 'warning',
                                RestaurantClaimStatus::APPROVED->value => 'success',
                                RestaurantClaimStatus::REJECTED->value => 'danger',
                                default => null,
                            };
                        })->badge(true),
                        TextEntry::make('approved_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('rejected_at')
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
                Section::make('Timestamps')
                    ->schema([
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->dateTime()
                            ->placeholder('-'),

                    ]),
            ])
            ->columns(1);
    }
}
