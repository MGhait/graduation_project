<?php

namespace App\Filament\Admin\Resources\UserResource\Widgets;

use App\Models\IC;
use App\Models\Message;
use App\Models\User;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;

class TotalNumber extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';
    protected function getStats(): array
    {
        return [
            Stat::make("Total Users", User::count())
                ->icon('heroicon-s-users')
                ->progress(69)
                ->progressBarColor(50)
                ->iconPosition('start')
                ->description('The users in this period')
                ->descriptionIcon('heroicon-o-chevron-right', 'after')
                ->iconColor('info'),
            Stat::make("Saved ICs", IC::sum('likes'))
                ->icon('heroicon-s-bookmark')
                ->progressBarColor(50)
                ->iconColor('secondary')
                ->iconPosition('start')
                ->description('User Joined Us last 30 days')
                ->descriptionIcon('heroicon-o-chevron-down', 'after')
                ->progress(20),
//            Stat::make("New users", User::where('created_at', '>=', $lastMonth)->count())
//                ->icon('heroicon-s-user')
//                ->progressBarColor(50)
//                ->iconColor('secondary')
//                ->iconPosition('start')
//                ->description('User Joined Us last 30 days')
//                ->descriptionIcon('heroicon-o-chevron-down', 'after')
//                ->progress(20),

            Stat::make("New Messages", Message::where('status', 'unread')->count())
                ->icon('heroicon-s-envelope')
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
