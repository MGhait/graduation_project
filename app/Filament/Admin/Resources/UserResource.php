<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Filament\Admin\Resources\UserResource\Pages\LocationMap;
use App\Filament\Admin\Resources\UserResource\RelationManagers;
use App\Filament\Admin\Resources\UserResource\Widgets\TotalNumber;
use App\Filament\Admin\Resources\UserResource\Widgets\UserLocationMap;
use App\Forms\Components\UserMapPicker;
use App\Models\Store;
use App\Models\User;
//use Dotswan\FilamentMapPicker\Fields\Map;
//use Dotswan\MapPicker\Facades\MapPicker;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Actions\Action;
use Filament\Forms\Set;


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('first_name')
                    ->maxLength(255)
                    ->default(null)
                    ->required(),
                Forms\Components\TextInput::make('last_name')
                    ->maxLength(255)
                    ->default(null)
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('username')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\DateTimePicker::make('email_verified_at')->visibleOn('show'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->maxLength(255)
                    ->confirmed()
                    ->visibleOn('create')
                    ->required(),
                Forms\Components\TextInput::make('password_confirmation')->visibleOn('create')
                    ->required(),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('otp')
                    ->maxLength(255)
                    ->default(null)
                    ->visibleOn('show'),
                Forms\Components\DateTimePicker::make('otp_till')
                    ->visibleOn('show'),
                Fieldset::make('Location')
                    ->schema([
                        Forms\Components\Group::make([
                            Map::make('location')
                                ->label('Mark Your Location')
                                ->defaultLocation(latitude: 31.035550845637, longitude: 31.361715495586) // Default to Mansura
                                ->clickable(true)
                                ->zoom(15)
                                ->minZoom(5)
                                ->maxZoom(18)
                                ->markerColor("#3b82f6")
                                ->liveLocation(true, true, 5000)
                                ->afterStateUpdated(function (Set $set, ?array $state): void {
                                    $set('latitude', $state['lat']);
                                    $set('longitude', $state['lng']);
                                })
                                ->afterStateHydrated(function (?array $state, $record, Set $set): void {
                                    if ($record) {
                                        $set('location', [
                                            'lat' => $record->latitude,
                                            'lng' => $record->longitude,
                                        ]);
                                    }
                                })->visible(fn(Component $component) => $component->getLivewire()->getForm('form')->getOperation() !== 'view'),
                            Forms\Components\Hidden::make('latitude')->dehydrated(),
                            Forms\Components\Hidden::make('longitude')->dehydrated(),
                        ])
                    ])->visible(fn(Component $component) => $component->getLivewire()->getForm('form')->getOperation() !== 'view'),
                ViewField::make('nearby_stores_map')
                    ->view('filament.components.near-shops')
                    ->visibleOn('view')
                    ->dehydrated(false),
                Forms\Components\TextInput::make('provider')
                    ->maxLength(255)
                    ->default(null)->visibleOn('show'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Full Name')
                    ->searchable(query: function (Builder $query, string $search) {
                        $query->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    }),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->placeholder('No Phone Number'),
                //                Tables\Columns\TextColumn::make('longitude')
                //                    ->searchable(),
                //                Tables\Columns\TextColumn::make('latitude')
                //                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('provider')
                    ->placeholder('Normal Login'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('view_map')
                    ->label('View Map')
                    ->url(fn($record) => LocationMap::getUrl(['record' => $record->username]))
                    ->icon('heroicon-o-map')
                    ->disabled(fn($record) => !$record->latitude || !$record->longitude)
                //                    ->tooltip(fn($record) => !$record->latitude || !$record->longitude ? 'Location not available' : null),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'location-map' => Pages\LocationMap::route('/{record}/location-map'),

        ];
    }
    public static function getWidgets(): array
    {
        return [
            UserLocationMap::class,
            TotalNumber::class
        ];
    }
}
