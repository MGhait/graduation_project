<?php

namespace App\Filament\Admin\Resources\TruthTableResource\Pages;

use App\Filament\Admin\Resources\TruthTableResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTruthTables extends ListRecords
{
    protected static string $resource = TruthTableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
