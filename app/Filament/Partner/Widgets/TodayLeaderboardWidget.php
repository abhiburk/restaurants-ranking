<?php

namespace App\Filament\Partner\Widgets;

use App\Models\Restaurant;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;

class TodayLeaderboardWidget extends TableWidget
{
    protected static ?string $heading = 'Top Restaurants Today';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Restaurant::query()
                    ->select('restaurants.*')
                    ->withCount(['votes' => function ($query) {
                        $query->where('voted_at', today());
                    }])
                    ->having('votes_count', '>', 0)
                    ->orderBy('votes_count', 'desc')
            )
            ->paginated(false)
            ->modifyQueryUsing(fn (Builder $query) => $query->limit(3))
            ->columns([
                TextColumn::make('index')->label('#')->rowIndex(),
                TextColumn::make('name')->label('Restaurant')->description(fn (Restaurant $record) => $record->address)->weight('bold'),
                TextColumn::make('votes_count')->formatStateUsing(fn ($state) => Number::abbreviate($state, 1))->label('Votes'),
            ]);
    }
}
