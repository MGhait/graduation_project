<?php

namespace App\Filament\Admin\Resources\ICResource\Widgets;

use App\Models\IC;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;

class TotalIC extends BaseWidget
{
    protected int|string|array $columnSpan = '6';
    protected function getStats(): array
    {
        return [
            Stat::make("Total Views", IC::sum('views'))
                ->icon('heroicon-o-arrow-trending-up')
                ->iconColor('primary')
                ->iconBackgroundColor('info')
                ->iconPosition('start')
                ->description("Sum Of All ICs Total Views ")
                ->descriptionIcon('heroicon-o-chevron-up', 'after')
                ->progress(80),
            Stat::make("Total Likes", IC::sum('likes'))
                ->icon('heroicon-s-heart')
                ->iconBackgroundColor('danger')
                ->iconColor('warning')
                ->iconPosition('start')
                ->description("Popular ICs based on User's preferences")
                ->descriptionIcon('heroicon-o-chevron-up', 'after')
                ->progressBarColor('danger')
                ->progress(20),
            Stat::make("Total ICs", IC::count())
                ->icon('heroicon-s-cpu-chip')
                ->progressBarColor(50)
                ->iconPosition('start')
                ->description('The ICs in our Database')
                ->descriptionIcon('heroicon-o-chevron-right', 'after')
                ->progressBarColor(50)
                ->iconColor('warning')
                ->chartColor('danger')
                ->progress(80),
        ];
    }
}
