<?php

namespace App\Filament\Admin\Resources\ICResource\Pages;

use App\Filament\Admin\Resources\ICResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListICS extends ListRecords
{
    protected static string $resource = ICResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ICResource\Widgets\TotalIC::class,
//            \App\Filament\Admin\Resources\ICResource\Widgets\TopViewedICs::class,
        ];
    }

}
