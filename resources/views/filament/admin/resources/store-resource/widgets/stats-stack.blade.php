
<x-filament-widgets::widget>
    <x-filament::section>
        <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow" style="--override-stat-grid: 1;">
            <style>
                [style*="--override-stat-grid"] .grid {
                    grid-template-columns: repeat(1, minmax(0, 1fr)) !important;
                }
            </style>
            <div class="mb-4">
                @livewire(App\Filament\Admin\Resources\StoreResource\Widgets\TotalStoresICs::class)
            </div>
            <div>
                @livewire(App\Filament\Admin\Resources\StoreResource\Widgets\TotalStores::class)
            </div>
        </div>

    </x-filament::section>
</x-filament-widgets::widget>
