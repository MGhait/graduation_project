<?php

namespace App\Filament\Admin\Resources\StoreResource\Pages;

use App\Filament\Admin\Resources\StoreResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewStore extends ViewRecord
{
    protected static string $resource = StoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
//            Actions\DeleteAction::make(),
        ];
    }


    public function getViewData(): array
    {
        return [
            'latitude' => $this->record->latitude,
            'longitude' => $this->record->longitude,
        ];
    }
}
