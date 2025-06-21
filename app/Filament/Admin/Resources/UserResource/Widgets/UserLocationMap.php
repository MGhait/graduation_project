<?php

namespace App\Filament\Admin\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Widgets\Widget;

class UserLocationMap extends Widget
{
    protected static string $view = 'filament.admin.resources.user-resource.widgets.user-location-map';

    protected int|string|array $columnSpan = 'full';


    public ?User $record = null;

    public static function canView(): bool
    {
        return request()->routeIs('filament.admin.resources.users.view');
    }

    public static function canEdit(): bool
    {
        return request()->routeIs('filament.admin.resources.users.edit');
    }

    public static function shouldBeVisible(): bool
    {
        return request()->routeIs([
            'filament.admin.resources.users.view',
            'filament.admin.resources.users.edit',
        ]);
    }

//    public function getRecord()
//    {
//        return request()->route('record');
//    }

    public function render(): \Illuminate\Contracts\View\View
    {
//        $username = $this->getRecord();
//        $user = User::where('username', $username)->findOrFail();

        return view(static::$view, [
            'latitude' => $this->record->latitude,
            'longitude' => $this->record->longitude,
            'name' => $this->record->name,
        ]);
    }
}
