<?php

namespace App\Filament\Admin\Resources\StoreResource\Widgets;

use Filament\Widgets\Widget;

class StatsStack extends Widget
{
    protected static string $view = 'filament.admin.resources.store-resource.widgets.stats-stack';
    protected int|string|array $columnSpan = '6';

}
