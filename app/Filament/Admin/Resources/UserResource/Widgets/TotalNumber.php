<?php

namespace App\Filament\Admin\Resources\UserResource\Widgets;

use App\Models\IC;
use App\Models\Store;
use App\Models\User;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;

class TotalNumber extends BaseWidget
{
    protected function getStats(): array
    {
        $lastMonth = now()->subMonth(2);
        return [
            Stat::make("Total Users",User::count())
                ->icon('heroicon-s-users')
                ->progress(69)
                ->progressBarColor(50)
                ->iconPosition('start')
                ->description('The users in this period')
                ->descriptionIcon('heroicon-o-chevron-right', 'after')
                ->iconColor('info'),
            Stat::make("New users", User::where('created_at', '>=', $lastMonth)->count())
                ->icon('heroicon-s-user')
                ->progressBarColor(50)
                ->iconColor('secondary')
                ->iconPosition('start')
                ->description('User Joined Us last 30 days')
                ->descriptionIcon('heroicon-o-chevron-down', 'after')
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
            Stat::make("Total Stores", Store::count()+1)
                ->icon('heroicon-s-eye')
                ->progressBarColor(50)
                ->iconPosition('start')
                ->description('Total Stores Worked With Us')
                ->descriptionIcon('heroicon-o-chevron-up', 'after')
                ->iconColor('light') // can be added form the AdminPanelProvider
                ->progress(80),
        ];
    }
}
