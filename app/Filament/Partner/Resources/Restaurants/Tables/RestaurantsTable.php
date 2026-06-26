<?php

namespace App\Filament\Partner\Resources\Restaurants\Tables;

use App\Enums\RestaurantStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RestaurantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->visibility('public')
                    ->defaultImageUrl('/restaurants/01KMF4YDWWE57X9ET2RBA8WEWP.jpg')
                    ->circular(),
                TextColumn::make('name')
                    ->lineClamp(1)
                    // ->icon(Heroicon::CheckBadge)
                    // ->iconColor('success')
                    // ->iconPosition(IconPosition::After)
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Category'),
                TextColumn::make('city.name')
                    ->label('City')
                    ->searchable(),

                TextColumn::make('status')
                ->color(fn (string $state): string => match ($state) {
                        RestaurantStatus::ACTIVE->value => 'success',
                        RestaurantStatus::INACTIVE->value => 'danger',
                        default => 'warning',
                    })
                    ->badge(),
                ToggleColumn::make('is_active')
                    ->label('Active'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // TrashedFilter::make(),
                SelectFilter::make('category_id')->label('Category')->relationship('category', 'name'),
                SelectFilter::make('city_id')->label('City')
                ->relationship('city', 'name', fn (Builder $query) => $query->where('is_active', 1)),
                TernaryFilter::make('is_active')->label('Active'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
