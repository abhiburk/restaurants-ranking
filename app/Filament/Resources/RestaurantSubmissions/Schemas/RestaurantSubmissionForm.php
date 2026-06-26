<?php

namespace App\Filament\Resources\RestaurantSubmissions\Schemas;

use App\Enums\Amenities;
use App\Enums\UserType;
use App\Models\Contributor;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class RestaurantSubmissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make()
                ->columns([
                    'default' => 1,
                    'sm' => 1,
                    'lg' => 3,
                ])
                ->schema([
                    Group::make()
                        ->columnSpan(['default' => 1, 'lg' => 2])
                        ->schema([
                            Tabs::make('Tabs')
                                ->tabs([
                                    Tab::make('About')
                                        ->icon(Heroicon::BuildingStorefront)
                                        ->schema([
                                            Select::make('contributor_id')
                                                ->label('Contributor')
                                                ->relationship('contributor', 'user.name')
                                                ->searchable()
                                                ->required(),
                                            TextInput::make('name')->label('Restaurant Name')->required(),
                                            Select::make('city_id')
                                                ->label('City')
                                                ->required()
                                                ->helperText('Your restaurant will be displayed in this city')
                                                ->searchable()
                                                ->preload()
                                                ->default(fn() => request()->query('city_id'))
                                                ->relationship('city', 'name', function (Builder $query) {
                                                    return $query->active()->where(function (Builder $q) {
                                                        // If the user is a contributor, exclude their own submissions
                                                        if (auth()->user()->role == UserType::CONTRIBUTOR->value) {
                                                            $q->orWhereHas('contributors', fn(Builder $query) => $query->where('user_id', '!=', auth()->id()));
                                                        }
                                                    });
                                                }),

                                            Textarea::make('description')
                                                ->rows(5)
                                                ->columnSpanFull(),
                                            Select::make('category_id')->required()->relationship('category', 'name'),
                                            TagsInput::make('amenities')
                                                ->label('Amenities')
                                                ->helperText('Add amenities to help customers find your restaurant. Press enter after each amenity.')
                                                ->suggestions(Amenities::values())
                                                ->columnSpanFull(),
                                            Flex::make([
                                                TimePicker::make('open_hours')->label('Opening Hours')->helperText('Your restaurants starting hours'),
                                                TimePicker::make('close_hours')->label('Closing Hours')->helperText('Your restaurants closing hours'),
                                            ]),

                                        ]),

                                    Tab::make('Contact')
                                        ->icon(Heroicon::Phone)
                                        ->schema([
                                            TextInput::make('email')
                                                ->label('Restaurant Email Address')
                                                ->helperText('Email will be shown to the end user')
                                                ->email(),
                                            TextInput::make('phone')
                                                ->label('Restaurant Phone Number')
                                                ->helperText('Phone number will be shown to the end user')
                                                ->tel(),
                                        ]),
                                    Tab::make('Address')
                                        ->icon(Heroicon::MapPin)
                                        ->schema([
                                            Map::make('location')
                                                ->label('Location')
                                                // ->defaultLocation(21.1466, 79.0489)
                                                ->liveLocation(true, false, 5000)
                                                // ->boundaries(true, 6.5, 68.0, 35.5, 97.5)  // India boundaries
                                                // ->zoom(8)
                                                // ->minZoom(10)  // Recommended to prevent zooming too far out
                                                ->afterStateUpdated(function (Set $set, ?array $state): void {
                                                    $set('latitude', $state['lat']);
                                                    $set('longitude', $state['lng']);
                                                })
                                                ->afterStateHydrated(function ($state, $record, Set $set): void {
                                                    $set('location', [
                                                        'lat' => $record->latitude ?? null,
                                                        'lng' => $record->longitude ?? null,
                                                        // 'geojson' => json_decode(strip_tags($record->description))
                                                    ]);
                                                })
                                                ->helperText('The map only allows you to select a location in India')
                                                ->columnSpanFull(),
                                            Group::make()
                                                ->schema([
                                                    TextInput::make('latitude')->numeric(),
                                                    TextInput::make('longitude')->numeric(),
                                                ])->columns(2),
                                            TextInput::make('radius')->default(100)->minValue(0)->postfix('meters')->numeric()->helperText('The radius of your restaurant in meters'),
                                            Textarea::make('address')->columnSpanFull()->helperText('Your restaurant address will be shown to the end user'),
                                            TextInput::make('locality')->helperText('Name of the city/town/village'),
                                            // TextInput::make('state'),
                                            // TextInput::make('country'),
                                            TextInput::make('postal_code'),

                                        ]),
                                    Tab::make('Gallary')
                                        ->icon(Heroicon::Photo)
                                        ->schema([
                                            SpatieMediaLibraryFileUpload::make('photos')
                                                ->columnSpanFull()
                                                ->helperText('Upload photos of your restaurant')
                                                ->visibility('public')
                                                ->image()
                                                ->multiple()
                                                ->directory('restaurants')
                                                ->collection('photos')
                                                ->reorderable()
                                                ->imageEditor(),
                                        ]),

                                ]),

                        ]),

                    Group::make()
                        ->columnSpan(['default' => 1, 'lg' => 1])
                        ->schema([

                            Section::make('')
                                ->schema([
                                    FileUpload::make('logo')
                                        ->columnSpanFull()
                                        ->helperText('Upload a logo for your restaurant')
                                        ->visibility('public')
                                        ->image()
                                        ->directory('restaurants')
                                        ->imageEditor(),
                                    FileUpload::make('banner')
                                        ->columnSpanFull()
                                        ->helperText('Upload a banner for your restaurant')
                                        ->visibility('public')
                                        ->image()
                                        ->directory('restaurants')
                                        ->imageEditor(),

                                ]),

                        ]),

                ])->columnSpanFull(),
        ]);
    }
}
