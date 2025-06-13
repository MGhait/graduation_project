<?php

namespace App\Filament\Admin\Resources\StoreResource\RelationManagers;

use App\Models\Store;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InventoryRelationManager extends RelationManager
{
    protected static string $relationship = 'inventory';

    public function form(Form $form): Form
    {
        return $form
//            ->schema([
//                Forms\Components\Hidden::make('store_id')
//                    ->default($this->ownerRecord->id),
//                Forms\Components\Select::make('ic_id')
//                    ->label('IC Item')
//                    ->relationship('ic', 'name') // Uses 'ic' relationship from ICStore model
//                    ->searchable()
//                    ->required(),
//
//                Forms\Components\TextInput::make('quantity')
//                    ->numeric()
//                    ->required(),
//                Forms\Components\TextInput::make('price')
//                    ->numeric()
//                    ->required(),
//            ]);
            ->schema([
                // Filament auto-fills store_id from ownerRecord
                Forms\Components\Select::make('ic_id')
                    ->label('IC Item')
                    ->relationship('ic', 'name')
                    ->searchable()
                    ->disabledOn('edit')
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('quantity')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
//            ->columns([
//                Tables\Columns\TextColumn::make('ic.commName')
//                    ->searchable()
//                    ->label('Name'),
//                // Simplified column configuration
//                Tables\Columns\TextColumn::make('ic.name')
//                    ->searchable()
//                    ->label('IC Code'),
//                Tables\Columns\TextColumn::make('quantity'),
//                Tables\Columns\TextColumn::make('price')->money('usd'),
//            ])
            ->columns([
                Tables\Columns\TextColumn::make('ic.commName')
                    ->searchable()
                    ->label('Display Name'),

                Tables\Columns\TextColumn::make('ic.name')
                    ->searchable()
                    ->label('Code'),

                Tables\Columns\TextColumn::make('quantity'),
                Tables\Columns\TextColumn::make('price')->money(),
            ])
//            ->headerActions([
//                Tables\Actions\CreateAction::make(),
////                Tables\Actions\AttachAction::make()
//            ])
//            ->actions([
//                Tables\Actions\EditAction::make(),
//                Tables\Actions\DeleteAction::make(),
//            ])
//            ->headerActions([
//                // Use AttachAction so it knows this is a belongsToMany / pivot relationship
//                Tables\Actions\AttachAction::make()
//                    ->relationship('inventory')
//                    ->buttonLabel('Add IC to Inventory')
//                    ->form([
//                        Forms\Components\Select::make('ic_id')
//                            ->label('IC Item')
//                            ->relationship('ic', 'name')
//                            ->searchable()
//                            ->required(),
//                        Forms\Components\TextInput::make('quantity')
//                            ->numeric()
//                            ->required(),
//                        Forms\Components\TextInput::make('price')
//                            ->numeric()
//                            ->required(),
//                    ]),
//            ])
//            ->headerActions([
//                Tables\Actions\AttachAction::make()
//                ->modalHeading('Attach IC to this Store')
//                    ->form(fn(Tables\Actions\AttachAction $action): array => [
//                        // this renders the select of all ICs
//                        $action->getRecordSelect(),
//                        // pivot fields:
//                        Forms\Components\TextInput::make('quantity')
//                            ->numeric()
//                            ->required(),
//                        Forms\Components\TextInput::make('price')
//                            ->numeric()
//                            ->required(),
//                    ]),
//            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->visible(true)
                    ->label('Attach IC')
                    ->modalHeading('Attach an IC')
                    ->visible(fn() => $this->getOwnerRecord()->exists)
                    ->form([
                        Forms\Components\Select::make('ic_id')
                            ->label('IC')
                            ->relationship('ic', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('quantity')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('price')
                            ->numeric()
                            ->required(),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),   // lets you update quantity/price
                Tables\Actions\DeleteAction::make()// detaches the IC from this store
                ->label('Detach')
                    ->icon('heroicon-o-x-mark')
                    ->modalHeading('Confirm IC Removal')
                    ->modalDescription('Are you sure you want to remove this IC from the store inventory?')
                    ->successNotificationTitle('IC Removed')  // <-- Title of the notification
                    ->after(function ($record, $livewire) {
                        Notification::make()
                            ->success()
                            ->title('Success')
                            ->body("IC '{$record->ic->name}' has been successfully removed.")
                            ->send();
                    }),
            ])
            ->filters([
                //
            ])
//            ->actions([
//                Tables\Actions\EditAction::make(),
//                Tables\Actions\DeleteAction::make(),
//            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label('Remove Selected'),
            ]);
    }

//    public function getRelationQuery(): Builder
//    {
//        // Customize the query for the related records
//        return parent::getRelationQuery()->with('ic'); // Load the IC model with the pivot
//    }

    public function isReadOnly(): bool
    {
        return false;
    }
    protected function getRelationshipQuery(): Builder
    {
        // This is the correct method to use in Filament v3 for relation managers
        return parent::getRelationshipQuery()->with('ic');
    }
}
