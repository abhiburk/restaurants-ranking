<?php

namespace App\Filament\Resources\RestaurantSubmissions\Tables;

use App\Enums\RestaurantSubmissionStatus;
use App\Models\RestaurantSubmission;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class RestaurantSubmissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->persistSortInSession()
            ->striped()
            ->columns([
                ImageColumn::make('logo')
                    ->visibility('public')
                    ->defaultImageUrl('/restaurants/01KMF4YDWWE57X9ET2RBA8WEWP.jpg')
                    ->circular(),
                TextColumn::make('name')
                ->copyable()
                    ->lineClamp(1)
                    ->searchable(),
                TextColumn::make('contributor.user.name')
                    ->label('Contributor'),
                TextColumn::make('category.name')
                    ->label('Category'),
                TextColumn::make('city.name')
                    ->label('City')
                    ->searchable(),
                TextColumn::make('status')
                    ->tooltip(fn (RestaurantSubmission $record) => $record->reason)
                    ->color(fn (string $state): string => match ($state) {
                        RestaurantSubmissionStatus::APPROVED->value => 'success',
                        RestaurantSubmissionStatus::REJECTED->value => 'danger',
                        default => 'warning',
                    })
                    ->badge(),
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
                SelectFilter::make('city_id')->label('City')->relationship('city', 'name', fn (Builder $query) => $query->where('is_active', 1)),
                TrashedFilter::make(), 
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                    Action::make('approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn(RestaurantSubmission $record) => $record->status == RestaurantSubmissionStatus::PENDING->value)
                        ->requiresConfirmation()
                        ->action(fn(RestaurantSubmission $record) => $record->updateStatus(RestaurantSubmissionStatus::APPROVED->value))
                        ->successNotificationTitle('Restaurant Approved'),

                    Action::make('reject')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn(RestaurantSubmission $record) => $record->status == RestaurantSubmissionStatus::PENDING->value)
                        ->schema([
                            Textarea::make('reason')->label('Rejection Reason')->required(),
                        ])
                        ->modalWidth(Width::Medium)
                        ->modalHeading('Reject Restaurant')
                        ->modalDescription('Rejecting a restaurant will make it inactive.')
                        ->successNotificationTitle('Restaurant Rejected')
                        ->action(function (array $data, RestaurantSubmission $record): void {
                            $record->updateStatus(RestaurantSubmissionStatus::REJECTED->value, $data['reason']);
                        }),
                    ])
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
                                fn(RestaurantSubmission $record) => $record->updateStatus('Approved')
                            );
                        }),

                    BulkAction::make('reject')
                        ->label('Reject Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->schema([
                            Textarea::make('reason')->required(),
                        ])
                        ->modalWidth(Width::Medium)
                        ->action(function (Collection $records, array $data): void {
                            $records->each(
                                fn(RestaurantSubmission $record) => $record->updateStatus(
                                    'Rejected',
                                    $data['reason']
                                )
                            );
                        }),
                ]),
            ]);
    } 
}
