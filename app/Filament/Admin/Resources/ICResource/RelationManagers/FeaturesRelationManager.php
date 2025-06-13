<?php

namespace App\Filament\Admin\Resources\ICResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FeaturesRelationManager extends RelationManager
{
    protected static string $relationship = 'features';
    protected static ?string $recordTitleAttribute = 'technology_family';


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('feature')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Current IC Feature')
            ->columns([
                Tables\Columns\TextColumn::make('feature'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data, RelationManager $livewire): array {
                    // $livewire->ownerRecord is the parent IC model
                    if ($detail = $livewire->ownerRecord->icDetail) {
                        $data['ic_details_id'] = $detail->id;
                    }
                    return $data;
                })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Feature added!')
                            ->body('Your new IC feature has been saved to the current IC.')
                    ),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()->successNotificationTitle('Feature Deleted Successfully!'),
            ])
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }

}
