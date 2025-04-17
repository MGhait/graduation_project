<?php

namespace App\Filament\Admin\Resources\ICResource\Pages;

use App\Filament\Admin\Resources\ICResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIC extends EditRecord
{
    protected static string $resource = ICResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
