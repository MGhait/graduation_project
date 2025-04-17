<?php

namespace App\Filament\Admin\Resources\ICResource\Pages;

use App\Filament\Admin\Resources\ICResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewIC extends ViewRecord
{
    protected static string $resource = ICResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
