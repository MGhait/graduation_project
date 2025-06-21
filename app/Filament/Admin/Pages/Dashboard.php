<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Resources\ICResource\Widgets\TopViewedICs;
use App\Filament\Admin\Resources\UserResource\Widgets\TotalNumber;
use App\Filament\Admin\Resources\UserResource\Widgets\UsersChart;
use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.admin.pages.dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            TotalNumber::class,
            TopViewedICs::class,
            UsersChart::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array|string
    {
        return 12;
    }
    protected function getFooterWidgets(): array
    {
        return [


        ];
    }

}
