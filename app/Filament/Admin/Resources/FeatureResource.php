<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FeatureResource\Pages;
use App\Filament\Admin\Resources\FeatureResource\RelationManagers;
use App\Models\Feature;
use App\Models\IC;
use App\Models\ICDetails;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;


class FeatureResource extends Resource
{
    protected static ?string $model = Feature::class;

    protected static ?string $navigationGroup = "IC Details";

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                    ->visible(fn($record) => $record !== null  ) // Only show when editing
                    ->dehydrated(false)
                    ->formatStateUsing(fn($record) =>$record->icDetail->ic->name ?? 'N/A'),

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
//                Forms\Components\Select::make('ic_details_id')
//                    ->relationship('icDetails', 'id')
//                    ->required(),
                Forms\Components\TextInput::make('feature')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('icDetail.ic.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('feature')
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
            ->groups([
                Tables\Grouping\Group::make('icDetail.ic.name')
                    ->label('By IC Chip')
                    ->collapsible(),
            ])
            ->defaultGroup('icDetail.ic.name')
            ->actions([
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeatures::route('/'),
            'create' => Pages\CreateFeature::route('/create'),
            'edit' => Pages\EditFeature::route('/{record}/edit'),
        ];
    }
}
