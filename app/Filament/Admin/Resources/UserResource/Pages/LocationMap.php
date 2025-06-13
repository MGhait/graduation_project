<?php
//
//namespace App\Filament\Admin\Resources\UserResource\Pages;
//
//use App\Filament\Admin\Resources\UserResource;
//use App\Models\User;
//use Filament\Resources\Pages\Page;
//
//class LocationMap extends Page
//{
//    protected static string $resource = UserResource::class;
//
//    protected static string $view = 'filament.admin.resources.user-resource.pages.location-map';
//
//    public User $record;
//    public ?float $latitude;
//    public ?float $longitude;
//
//    public function mount($record): void
//    {
//        $this->record = User::findOrFail($record);
//        $this->latitude = $this->record->latitude ? (float)$this->record->latitude : null;
//        $this->longitude = $this->record->longitude ? (float)$this->record->longitude : null;
//    }
//
//    public function getViewData(): array
//    {
//        return [
//            'mapData' => [
//                'latitude' => $this->latitude,
//                'longitude' => $this->longitude,
//                'hasLocation' => !is_null($this->latitude) && !is_null($this->longitude)
//            ]
//        ];
//    }
//}


namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use App\Models\User;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class LocationMap extends Page
{
    use InteractsWithRecord;
    protected static string $resource = UserResource::class;
    protected static string $view = 'filament.admin.resources.user-resource.pages.location-map';

    public ?float $latitude;
    public ?float $longitude;
    Public ?string $name;

    public function mount($record): void
    {
        $this->record = $this->resolveRecord($record); // important!
        $this->name = $this->record->name;
        $this->latitude = $this->record->latitude ? (float)$this->record->latitude : null;
        $this->longitude = $this->record->longitude ? (float)$this->record->longitude : null;
    }

    public function getViewData(): array
    {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'hasLocation' => !is_null($this->latitude) && !is_null($this->longitude), // $this->record->has_location
            'record' => $this->record ?? null,
        ];
    }
}
