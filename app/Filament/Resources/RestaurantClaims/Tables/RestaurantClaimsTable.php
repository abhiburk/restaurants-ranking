<?php

namespace App\Filament\Resources\RestaurantClaims\Tables;

use App\Enums\RestaurantClaimStatus;
use App\Models\RestaurantClaim;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class RestaurantClaimsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->persistSortInSession()
            ->striped()
            ->paginated([25, 50, 100])

            ->columns([
                TextColumn::make('user.name')
                    ->label('Claimed By')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('restaurant.name')
                    ->label('Restaurant')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('city.name')
                    ->label('City')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('notes')
                    ->limit(40)
                    ->tooltip(fn($record) => $record->notes)
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        RestaurantClaimStatus::PENDING => 'success',
                        RestaurantClaimStatus::REJECTED => 'danger',
                        default => 'warning',
                    })
                    ->tooltip(function (RestaurantClaim $record) {
                        return $record->status == RestaurantClaimStatus::REJECTED->value ? $record->reason : '';
                    })
                    ->sortable(),

                

                TextColumn::make('reviewer.name')
                    ->label('Reviewed By')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('reviewed_at')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Claimed At')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('city_id')
                    ->label('City')
                    ->relationship('city', 'name')
                    ->searchable()
                    ->preload(),
            ])

            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),

                    EditAction::make(),

                    Action::make('approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn(RestaurantClaim $record) => $record->status == RestaurantClaimStatus::PENDING->value)
                        ->requiresConfirmation()
                        ->successNotificationTitle('Restaurant Approved Successfully')
                        ->action(fn(RestaurantClaim $record) => $record->updateStatus(RestaurantClaimStatus::APPROVED->value)),

                    Action::make('reject')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn(RestaurantClaim $record) => $record->status == RestaurantClaimStatus::PENDING->value)
                        ->schema([
                            Textarea::make('reason')
                                ->label('Rejection Reason')
                                ->required(),
                        ])
                        ->modalWidth(Width::Medium)
                        ->successNotificationTitle('Restaurant Claim Rejected Successfully')
                        ->action(function (array $data, RestaurantClaim $record): void {
                            $record->updateStatus(RestaurantClaimStatus::REJECTED->value, $data['reason']);
                        }),
                ]),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    ForceDeleteBulkAction::make(),

                    RestoreBulkAction::make(),

                    BulkAction::make('approve')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $records->each(
                                fn(RestaurantClaim $record) => $record->updateStatus(RestaurantClaimStatus::APPROVED->value)
                            );
                        }),

                    BulkAction::make('reject')
                        ->label('Reject Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->schema([
                            Textarea::make('reason')
                                ->required(),
                        ])
                        ->modalWidth(Width::Medium)
                        ->action(function (Collection $records, array $data): void {
                            $records->each(
                                fn(RestaurantClaim $record) => $record->updateStatus(
                                    RestaurantClaimStatus::REJECTED->value,
                                    $data['reason']
                                )
                            );
                        }),
                ]),
            ]);
    }
}
