<?php

namespace App\Filament\Admin\Resources\StoreResource\Pages;

use App\Filament\Admin\Resources\StoreResource;

use App\Filament\Admin\Resources\StoreResource\Widgets\ICStoresChart;
use App\Filament\Admin\Resources\UserResource\Widgets\TotalNumber;
use Filament\Actions;
use Filament\Infolists\Components\Grid;
use Filament\Resources\Pages\ListRecords;

class ListStores extends ListRecords
{
    protected static string $resource = StoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getHeaderWidgetsColumns(): int|array|string
    {
        return 12;
    }

    protected function getHeaderWidgets(): array{
        return [
            ICStoresChart::class,
            StoreResource\Widgets\StatsStack::class,
        ];
    }
}
