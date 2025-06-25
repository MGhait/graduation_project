<?php

namespace App\Filament\Admin\Resources\StoreResource\Widgets;

use EightyNine\FilamentAdvancedWidget\AdvancedChartWidget;
use Illuminate\Support\Facades\DB;

class ICStoresChart extends AdvancedChartWidget
{
    protected static ?string $heading = 'ICs Quantity Distribution by Store';
    protected static string $color = 'info';
    protected static ?string $icon = 'heroicon-o-cube';
    protected static ?string $iconColor = 'success';
    protected static ?string $iconBackgroundColor = 'success';
    protected static ?string $description = 'Total ICs quantity distributed across different stores';

    protected int|string|array $columnSpan = '6';
    protected static ?string $descriptionIcon = 'heroicon-o-information-circle';
    protected static ?string $descriptionColor = 'primary';
    protected static ?string $descriptionIconPosition = 'after';
    protected static bool $isLazy = true;
    protected static ?string $loadingIndicator = 'Loading ICs quantity data...';
    protected static ?string $emptyStateHeading = 'No ICs found';
    protected static ?string $emptyStateDescription = 'There are no ICs quantities to display in the chart.';
    protected static ?string $emptyStateIcon = 'heroicon-o-exclamation-triangle';

    protected static ?array $options = [
        'plugins' => [
            'legend' => [
                'display' => true,
                'position' => 'bottom',
                'labels' => [
                    'usePointStyle' => true,
                    'padding' => 20,
                    'font' => [
                        'size' => 16,
                        'weight' => 'bold'
                    ],
                ]
            ],
            'tooltip' => [
                'enabled' => true,
                'backgroundColor' => 'rgba(0, 0, 0, 0.8)',
                'titleColor' => '#fff',
                'bodyColor' => '#fff',
                'borderColor' => '#fff',
                'borderWidth' => 1,
                'cornerRadius' => 8,
                'displayColors' => true,
                'callbacks' => [
                    'label' => 'function(context) {
                        return context.label + ": " + context.parsed + " ICs (" +
                               Math.round((context.parsed / context.dataset.data.reduce((a, b) => a + b, 0)) * 100) + "%)";
                    }'
                ]
            ],
            'datalabels' => [
                'display' => true,
                'color' => '#fff',
                'backgroundColor' => 'rgba(0, 0, 0, 0.7)', // Optional: add background
                'borderRadius' => 4, // Optional: rounded background
                'padding' => 4, // Optional: padding around text
                'font' => [
                    'weight' => 'bold',
                    'size' => 14,
                    'family' => 'Arial'
                ],
                'textStrokeWidth' => 0, // Remove text stroke
//                'formatter' => 'function(value, context) {
//                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
//                    const percentage = Math.round((value / total) * 100);
//                    return percentage > 5 ? percentage + "%" : "";
//                }'
                'formatter' => 'function(value, context) {
                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                    const percentage = Math.round((value / total) * 100);
                    return percentage + "%";
                }'
            ]
        ],
        'scales' => [
            'x' => ['display' => false],
            'y' => ['display' => false]
        ],
        'responsive' => true,
        'maintainAspectRatio' => false,
        'cutout' => '0%', // 0% for pie chart, 50% for doughnut
        'radius' => '80%',
        'animation' => [
            'animateRotate' => true,
            'animateScale' => true,
            'duration' => 1500,
            'easing' => 'easeInOutQuart'
        ],
        'elements' => [
            'arc' => [
                'borderWidth' => 2,
                'borderColor' => '#fff'
            ]
        ]
    ];

    public function getType(): string
    {
        return 'pie';
    }

    public function getData(): array
    {
        // Get ICs quantity per store from pivot table
        $icsQuantityPerStore = DB::table('ic_store')
            ->join('stores', 'stores.id', '=', 'ic_store.store_id')
            ->select('stores.name as store_name', DB::raw('SUM(ic_store.quantity) as total_quantity'))
            ->groupBy('stores.id', 'stores.name')
            ->orderBy('total_quantity', 'desc')
            ->get();

        if ($icsQuantityPerStore->isEmpty()) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
            '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF',
            '#4BC0C0', '#FF6384', '#36A2EB', '#FFCE56'
        ];

        return [
            'datasets' => [
                [
                    'label' => 'ICs Quantity per Store',
                    'data' => $icsQuantityPerStore->pluck('total_quantity')->toArray(),
                    'backgroundColor' => array_slice($colors, 0, $icsQuantityPerStore->count()),
                    'borderColor' => array_map(function ($color) {
                        return $color . 'CC'; // Add transparency
                    }, array_slice($colors, 0, $icsQuantityPerStore->count())),
                    'borderWidth' => 2,
                    'hoverBackgroundColor' => array_map(function ($color) {
                        return $color . 'DD'; // Slightly more opaque on hover
                    }, array_slice($colors, 0, $icsQuantityPerStore->count())),
                    'hoverBorderColor' => '#fff',
                    'hoverBorderWidth' => 3,
                ]
            ],
            'labels' => $icsQuantityPerStore->pluck('store_name')->toArray(),
        ];
    }
}
