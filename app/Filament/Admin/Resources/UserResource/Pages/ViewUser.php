<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use App\Filament\Admin\Resources\UserResource\Widgets\UserLocationMap;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

//    protected function getFooterWidgets(): array
//    {
//        return [
//            UserLocationMap::class,
//        ];
//    }

    public function getViewData(): array
    {
        return [
            'latitude' => $this->record->latitude,
            'longitude' => $this->record->longitude,
        ];
    }
}
