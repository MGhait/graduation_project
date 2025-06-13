<?php

namespace App\Filament\Admin\Resources\StoreResource\RelationManagers;

use App\Models\IC;
use Filament\Actions\EditAction;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IcsRelationManager extends RelationManager
{
    protected static string $relationship = 'ics';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
            ])
            ->filters([
                //
            ])
//            ->headerActions([
//                // Attach existing IC
//                Action::make('AttachIC')
//                    ->label('Attach IC')
//                    ->form([
//                        Forms\Components\Select::make('ic_id')
//                            ->label('IC')
//                            ->options(IC::pluck('name', 'id'))
//                            ->searchable()
//                            ->required(),
//                        Forms\Components\TextInput::make('price')
//                            ->label('Price')
//                            ->numeric()
//                            ->required(),
//                        Forms\Components\TextInput::make('quantity')
//                            ->label('Quantity')
//                            ->numeric()
//                            ->required(),
//                    ])
//                    ->action(function (array $data, $livewire) {
//                        $store = $livewire->ownerRecord;
//
//                        $store->ics()->attach($data['ic_id'], [
//                            'price' => $data['price'],
//                            'quantity' => $data['quantity'],
//                        ]);
//
//                        Notification::make()
//                            ->success()
//                            ->title('IC Attached')
//                            ->body('The IC has been attached to the store.')
//                            ->send();
//                    })
//                    ->modalHeading('Attach Existing IC'),

//            ])
            ->headerActions([
                Action::make('attach_ic')
                    ->label('Attach IC')
                    ->modalHeading('Attach Existing IC')
                    ->form([
                        Forms\Components\Select::make('ic_id')
                            ->label('IC')
                            ->options(\App\Models\IC::pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('price')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('quantity')
                            ->numeric()
                            ->required(),
                    ])
                    ->action(function (array $data, RelationManager $livewire) {
                        /** @var \App\Models\Store $store */
                        $store = $livewire->ownerRecord;

                        $store->inventory()->attach($data['ic_id'], [
                            'price' => $data['price'],
                            'quantity' => $data['quantity'],
                        ]);

                        Notification::make()
                            ->success()
                            ->title('IC Attached')
                            ->body('The IC has been attached to the store.')
                            ->send();
                    }),
//            ->headerActions([
//                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
//                Tables\Actions\EditAction::make()
            ])
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
            ]);
    }
}
