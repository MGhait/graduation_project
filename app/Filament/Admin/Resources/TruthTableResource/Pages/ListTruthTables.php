<?php

namespace App\Filament\Admin\Resources\TruthTableResource\Pages;

use App\Filament\Admin\Resources\TruthTableResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTruthTables extends ListRecords
{
    protected static string $resource = TruthTableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getViewData(): array
    {
        return array_merge(parent::getViewData(), [
            'hideDefaultHeader' => true,
            'hideCreateButton' => true,
        ]);
    }

    // Override breadcrumbs method to ensure they're available
    public function getBreadcrumbs(): array
    {
        return [
//            route('filament.admin.pages.dashboard') => 'Dashboard',
//            route('filament.admin.resources.truth-tables.index') => 'Truth Tables',
        ];
    }

    // Better approach: Override the header rendering method
//    protected function hasHeader(): bool
//    {
//        return false; // This disables the default header completely
//    }

    // Or override specific header parts
//    public function getHeader(): ?\Illuminate\Contracts\View\View
//    {
//        return null; // This returns no header
//    }

    // Ensure breadcrumbs are still available for topbar
//    public function getBreadcrumbs(): array
//    {
//        return [
//            route('filament.admin.pages.dashboard') => 'Dashboard',
//            static::getResource()::getUrl() => static::getResource()::getLabel(),
//        ];
//    }

    // Make sure heading is available for topbar
//    public function getHeading(): string
//    {
//        return static::getResource()::getLabel();
//    }


//    public function getTitle(): string
//    {
//        return 'truth';
//    }
}
