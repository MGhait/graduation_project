<?php

namespace App\Filament\Admin\Resources\StoreResource\Widgets;

use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class TotalStoresICs extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make("total ic count in stores", DB::table('ic_store')->sum('quantity'))
                ->icon('heroicon-s-circle-stack')
                ->progressBarColor(50)
                ->iconColor('secondary')
                ->iconPosition('start')
                ->description('User Joined Us last 30 days')
                ->descriptionIcon('heroicon-o-chevron-down', 'after')
                ->progress(20),
        ];
    }
}
