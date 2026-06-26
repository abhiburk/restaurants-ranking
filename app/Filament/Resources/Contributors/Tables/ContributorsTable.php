<?php

namespace App\Filament\Resources\Contributors\Tables;

use App\Enums\ContributorApplicationStatus;
use App\Enums\ContributorStatus;
use App\Models\Contributor;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class ContributorsTable
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
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('city.name')
                    ->label('City')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('known_areas')
                    ->searchable(),
                TextColumn::make('status')
                    ->color(fn(string $state): string => match ($state) {
                        ContributorStatus::PENDING => 'success',
                        ContributorStatus::REJECTED => 'danger',
                        default => 'warning',
                    })
                    ->tooltip(function (Contributor $record) {
                        return $record->status == ContributorStatus::REJECTED->value ? $record->reason : '';
                    })
                    ->badge(),
                TextColumn::make('reviewer.name')
                    ->label('Reviewed By')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('reviewed_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('joined_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
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
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(ContributorStatus::class),
            ])
            ->recordActions([
                // EditAction::make(),

                Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(Contributor $record) => $record->status !== 'Approved')
                    ->requiresConfirmation()
                    ->action(fn(Contributor $record) => $record->updateStatus('Approved')),

                Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(Contributor $record) => $record->status !== 'Rejected')
                    ->schema([
                        Textarea::make('reason')
                            ->label('Rejection Reason')
                            ->required(),
                    ])
                    ->modalWidth(Width::Medium)
                    ->action(function (array $data, Contributor $record): void {
                        $record->updateStatus('Rejected', $data['reason']);
                    }),
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
                                fn(Contributor $record) => $record->updateStatus('Approved')
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
                                fn(Contributor $record) => $record->updateStatus(
                                    'Rejected',
                                    $data['reason']
                                )
                            );
                        }),
                ]),
            ]);
    }
}
