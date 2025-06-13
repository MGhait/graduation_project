<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ParameterResource\Pages;
use App\Filament\Admin\Resources\ParameterResource\RelationManagers;
use App\Models\IC;
use App\Models\ICDetails;
use App\Models\Parameter;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ParameterResource extends Resource
{
    protected static ?string $model = Parameter::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = "IC Details";

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['icDetail.ic']);
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('ic_name')
                    ->label('Current IC')
                    ->disabled()
                    ->visible(fn($record) => $record !== null) // Only show when editing
                    ->dehydrated(false)
                    ->formatStateUsing(fn($record) => $record->icDetail->ic->name ?? 'N/A'),
//                    ->columnSpanFull(),
                // IC selection for new entries
                Select::make('ic_id')
                    ->label('Select IC')
                    ->options(IC::all()->pluck('name', 'id'))
                    ->required()
                    ->visible(fn($record) => $record === null) // Only show when creating
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $icDetail = ICDetails::where('ic_id', $state)->first();
//                        dd($icDetail);
                        $set('ic_details_id', $icDetail->id ?? null);
                    }),

                Hidden::make('ic_details_id')
                    ->required(),
                Forms\Components\TextInput::make('technology_family')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('min_voltage')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('max_voltage')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('channels_number')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('inputs_per_channel')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('min_temperature')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('max_temperature')
                    ->numeric()
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('icDetail.ic.name')
                    ->label('Current IC')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('technology_family')
                    ->searchable(),
                Tables\Columns\TextColumn::make('min_voltage')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_voltage')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('channels_number')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('inputs_per_channel')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_temperature')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_temperature')
                    ->numeric()
                    ->sortable(),
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListParameters::route('/'),
            'create' => Pages\CreateParameter::route('/create'),
            'edit' => Pages\EditParameter::route('/{record}/edit'),
        ];
    }
}
