<?php

namespace App\Filament\Admin\Resources\StoreResource\Widgets;


use App\Models\Store;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;

class TotalStores extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';
    protected function getStats(): array
    {
        return [
            Stat::make("Total Stores", Store::count() + 1)
                ->icon('heroicon-s-shopping-bag')
                ->progressBarColor(50)
                ->iconPosition('start')
                ->description('Total Stores Worked With Us')
                ->descriptionIcon('heroicon-o-chevron-up', 'after')
                ->iconColor('light') // can be added form the AdminPanelProvider
                ->progress(80),
        ];
    }
}
