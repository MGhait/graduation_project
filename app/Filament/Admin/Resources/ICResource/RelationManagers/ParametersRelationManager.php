<?php

namespace App\Filament\Admin\Resources\ICResource\RelationManagers;

use App\Models\IC;
use App\Models\ICDetails;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ParametersRelationManager extends RelationManager
{
    protected static string $relationship = 'parameters';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('ic_details_id')
                    ->default(function () {
                        return $this->getOwnerRecord()->icDetail->id;
                })
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

//    public function save(): void
//    {
//        try {
//            $this->model->parameters()->create($this->data);
//        } catch (\Exception $e) {
//            \Log::error('Error saving parameter: ' . $e->getMessage());
//            throw $e;
//        }
//    }




    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Current IC Parameter')
            ->columns([
                Tables\Columns\TextColumn::make('technology_family'),
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
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([

            ]);
    }


    public function isReadOnly(): bool
    {
        return false;
    }

}
