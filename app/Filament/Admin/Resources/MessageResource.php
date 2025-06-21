<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MessageResource\Pages;
use App\Filament\Admin\Resources\MessageResource\RelationManagers;
use App\Models\Message;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->disabled(),
                Forms\Components\TextInput::make('email')->disabled(),
                Forms\Components\TextInput::make('subject')->disabled(),
                Forms\Components\TextInput::make('status')->disabled(),
                Forms\Components\Textarea::make('message')->columnSpanFull()->disabled()->rows(6),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->toggleable()->searchable(),
                TextColumn::make('email')->toggleable()->searchable()->copyable(),
                TextColumn::make('subject')->toggleable()->searchable(),
                BadgeColumn::make('status')
                    ->colors([
                        'primary' => 'unread',
                        'success' => 'read',
                    ])
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('created_at')->toggleable()->label("Sent At")->since()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'unread' => 'Unread',
                        'read' => 'Read',
                    ]),
            ])
            ->actions([
//                Tables\Actions\Action::make('markAsRead')
//                    ->label('')
//                    ->icon('heroicon-o-check-circle')
//                    ->iconSize('lg')
//                    ->visible(fn($record) => $record->status === 'unread')
//                    ->action(fn($record) => $record->update(['status' => 'read']))
//                    ->color('success'),

                Tables\Actions\ViewAction::make()->iconSize('lg')->label(""),

                Tables\Actions\DeleteAction::make()
                    ->label("")
                    ->icon('heroicon-o-trash')
                    ->iconSize('lg')
                    ->color('danger'),
            ])
//            ->actions([
//                Tables\Actions\ViewAction::make(),
//                Tables\Actions\DeleteAction::make(),
//            ])
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

    public static function canCreate(): bool
    {
        return false;
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMessages::route('/'),
            'create' => Pages\CreateMessage::route('/create'),
            'view' => Pages\ViewMessage::route('/{record}'),
            'edit' => Pages\EditMessage::route('/{record}/edit'),
        ];
    }
}
