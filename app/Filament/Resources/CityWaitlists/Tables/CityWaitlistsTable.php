<?php

namespace App\Filament\Resources\CityWaitlists\Tables;

use App\Jobs\SendCityWaitlistNotificationJob;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CityWaitlistsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')

            ->columns([
                TextColumn::make('user.name')
                    ->label('Registered User')
                    ->placeholder('Guest User')
                    ->searchable(),

                IconColumn::make('user_id')
                    ->label('Account')
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedUser)
                    ->tooltip(fn ($record) => $record->user_id
                        ? 'Registered User'
                        : 'Guest User'),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email copied')
                    ->copyMessageDuration(1500),

                TextColumn::make('city.name')
                    ->label('City')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('notification_status')
                    ->badge()
                    ->label('Status')
                    ->getStateUsing(fn ($record) => $record->sent_at ? 'Sent' : 'Pending')
                    ->colors([
                        'success' => 'Sent',
                        'warning' => 'Pending',
                    ]),

                TextColumn::make('sent_at')
                    ->label('Sent At')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Not Sent'),

                TextColumn::make('created_at')
                    ->label('Joined At')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([
                Filter::make('pending')
                    ->label('Pending Notifications')
                    ->query(fn (Builder $query) => $query->whereNull('sent_at')),

                Filter::make('sent')
                    ->label('Already Notified')
                    ->query(fn (Builder $query) => $query->whereNotNull('sent_at')),

                SelectFilter::make('city_id')
                    ->label('City')
                    ->relationship('city', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('joined_between')
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $query, $date) => $query->whereDate('created_at', '>=', $date)
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, $date) => $query->whereDate('created_at', '<=', $date)
                            );
                    }),
            ])

            ->recordActions([
                Action::make('notify')
                    ->label(fn ($record) => $record->sent_at
                        ? 'Resend Email'
                        : 'Send Email')
                    ->icon(Heroicon::Envelope)

                    ->requiresConfirmation()

                    ->action(function ($record): void {
                        $record->sendNotification();
                    })

                    ->successNotificationTitle('Notification queued successfully'),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    BulkAction::make('notify_pending')
                        ->label('Send To Pending')
                        ->icon(Heroicon::Envelope)
                        ->requiresConfirmation()

                        ->action(function (Collection $records): void {
                            $records->each->sendNotification();
                        })

                        ->successNotificationTitle(
                            'Notifications have been queued'
                        ),
                ]),
            ]);
    }
}
