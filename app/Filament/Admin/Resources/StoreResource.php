<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\StoreResource\Pages;
use App\Filament\Admin\Resources\StoreResource\RelationManagers;
use App\Models\Store;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
class StoreResource extends Resource
{
    protected static ?string $model = Store::class;
    protected static $editing = false;
    protected static $creating = false;



    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected function isEditing(): bool
    {
        self::$editing = request()->routeIs('filament.admin.resources.stores.edit');
        return self::$editing;
    }
    protected function isCreating(): bool
    {
        self::$creating = request()->routeIs('filament.admin.resources.stores.edit');
        return self::$creating;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255)->columnSpanFull(),
                    Fieldset::make('Location')
                        ->schema([
                            // Only show these fields when has_location is true
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
                                        $set('longtitude', $state['lng']);
                                    })
                                    ->afterStateHydrated(function (?array $state, $record, Set $set): void {
                                        if ($record) {
                                            $set('location', [
                                                'lat' => $record->latitude,
                                                'lng' => $record->longtitude,
                                            ]);
                                        }
                                    }),
//                                Map::make('static-map')
//                                    ->label('Shop Location')
//                                    ->default(function ($record) {
//                                        // This sets both the marker and the center of the map
//                                        if ($record) {
//                                            return [
//                                                'lat' => $record->latitude ?? 30.0,  // fallback defaults
//                                                'lng' => $record->longtitude ?? 30.0,
//                                            ];
//                                        }
//
//                                        return ['lat' => 30.0, 'lng' => 30.0]; // default for "create" mode
//                                    })
//                                    ->clickable(false)
//                                    ->draggable(false)
//                                    ->zoom(15)
//                                    ->minZoom(6)
//                                    ->maxZoom(18)
//                                    ->markerColor("#336786")
//                                    ->liveLocation(true, true, 5000)
//                                    ->afterStateUpdated(function (Set $set, ?array $state): void {
//                                        $set('latitude', $state['lat']);
//                                        $set('longtitude', $state['lng']);
//                                    })
//                                    ->afterStateHydrated(function (?array $state, $record, Set $set): void {
//                                        if ($record) {
//                                            $set('static-map', [
//                                                'lat' => $record->latitude,
//                                                'lng' => $record->longtitude,
//                                            ]);
//                                        }
//                                    })->visible(fn(Component $component) => $component->getLivewire()->getForm('form')->getOperation() == 'view'),
                                Forms\Components\Hidden::make('latitude')->dehydrated(),
                                Forms\Components\Hidden::make('longtitude')->dehydrated(),
                            ])->visible(function (Component $component) {
                                $livewire = $component->getLivewire();
                                return $livewire instanceof Pages\EditStore || $livewire instanceof Pages\CreateStore;
                            }),
                        Forms\Components\ViewField::make('store-location-map')
                            ->view('filament.components.store-location')
                            ->visible(function (Component $component) {
                                $livewire = $component->getLivewire();
                                return $livewire instanceof Pages\ViewStore;
                            })
                            ->dehydrated(false),
                        ]),
                ])->columnSpanFull()
            ]);
    }

    /**
     * Helper method to determine the current form operation.
     */
    protected static function getFormOperation(): string
    {
        // Get the current route name
        $routeName = request()->route()?->getName();

        if (str_contains($routeName, 'create')) {
            return 'create';
        } elseif (str_contains($routeName, 'edit')) {
            return 'edit';
        } else {
            return 'view';
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                    Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\InventoryRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStores::route('/'),
            'create' => Pages\CreateStore::route('/create'),
            'view' => Pages\ViewStore::route('/{record}'),
            'edit' => Pages\EditStore::route('/{record}/edit'),
        ];
    }
}
