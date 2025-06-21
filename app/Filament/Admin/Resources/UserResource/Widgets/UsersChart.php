<?php

namespace App\Filament\Admin\Resources\UserResource\Widgets;

use EightyNine\FilamentAdvancedWidget\AdvancedChartWidget;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UsersChart extends AdvancedChartWidget
{
    protected static string $color = 'info';
    protected static ?string $icon = 'heroicon-o-chart-bar';
    protected static ?string $iconColor = 'info';
    protected static ?string $iconBackgroundColor = 'info';
    protected static ?string $label = 'Users Chart';

    protected static ?string $badge = 'new';
    protected static ?string $badgeColor = 'success';
    protected static ?string $badgeIcon = 'heroicon-o-check-circle';
    protected static ?string $badgeIconPosition = 'after';

    protected static ?string $badgeSize = 'sm';
    protected int|string|array $columnSpan = '8';



    public ?string $filter = 'quarter';

    protected function getFilters(): ?array
    {
        return [
//            'week' => 'Last 7 Days',
            'month' => 'Last 30 Days',
            'quarter' => 'Last 3 Months',
            'year' => 'Last 12 Months',
        ];
    }

    protected function getData(): array
    {
//        return [
//            'datasets' => [
//                [
//                    'label' => 'Blog posts created',
//                    'data' => [0, 10, 5, 2, 21, 32, 45, 74, 65, 45, 77, 89],
//                ],
//            ],
//            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
//        ];
        $filter = $this->filter ?? 'month';

        switch ($filter) {
//            case 'today':
//                return $this->getTodayData();
//            case 'week':
//                return $this->getWeekData();
            case 'month':
                return $this->getMonthData();
            case 'quarter':
                return $this->getQuarterData();
            case 'year':
                return $this->getYearData();
            default:
                return $this->getMonthData();
        }

    }

    private function getTodayData(): array
    {
        $data = [];
        $labels = [];


        return [
            'datasets' => [
                [
                    'label' => 'Users Registered (Today)',
                    'data' => $data,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    private function getWeekData(): array
    {
        $data = [];
        $labels = [];

        // Get daily data for last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('M j');

            $count = User::whereDate('created_at', $date->toDateString())->count();
            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Users Registered (Last 7 Days)',
                    'data' => $data,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.5)',
                    'borderColor' => 'rgb(16, 185, 129)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    private function getMonthData(): array
    {
        $data = [];
        $labels = [];

        // Get daily data for last 30 days
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('M j');

            $count = User::whereDate('created_at', $date->toDateString())->count();
            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Users Registered (Last 30 Days)',
                    'data' => $data,
                    'backgroundColor' => 'rgba(139, 92, 246, 0.5)',
                    'borderColor' => 'rgb(139, 92, 246)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    private function getQuarterData(): array
    {
        $data = [];
        $labels = [];

        // Get weekly data for last 3 months (12 weeks)
        for ($i = 11; $i >= 0; $i--) {
            $startOfWeek = Carbon::now()->subWeeks($i)->startOfWeek();
            $endOfWeek = Carbon::now()->subWeeks($i)->endOfWeek();

            $labels[] = $startOfWeek->format('M j');

            $count = User::whereBetween('created_at', [
                $startOfWeek->toDateString(),
                $endOfWeek->toDateString()
            ])->count();

            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Users Registered (Last 3 Months)',
                    'data' => $data,
                    'backgroundColor' => 'rgba(245, 158, 11, 0.5)',
                    'borderColor' => 'rgb(245, 158, 11)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    private function getYearData(): array
    {
        $data = [];
        $labels = [];

        // Get monthly data for last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M Y');

            $count = User::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Users Registered (Last 12 Months)',
                    'data' => $data,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.5)',
                    'borderColor' => 'rgb(239, 68, 68)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    public function getHeading(): ?string
    {
        $filter = $this->filter ?? 'month';
        $total = $this->getTotalUsers($filter);

        return number_format($total);
    }

    private function getTotalUsers(string $filter): int
    {
        switch ($filter) {
            case 'today':
                return User::whereDate('created_at', today())->count();
            case 'week':
                return User::where('created_at', '>=', Carbon::now()->subDays(7))->count();
            case 'month':
                return User::where('created_at', '>=', Carbon::now()->subDays(30))->count();
            case 'quarter':
                return User::where('created_at', '>=', Carbon::now()->subMonths(3))->count();
            case 'year':
                return User::where('created_at', '>=', Carbon::now()->subYear())->count();
            default:
                return User::count();
        }
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'height' => '600px',
            'width' => '500px',
            'aspectRatio' => .5,
//            'devicePixelRatio' => 1,
            'interaction' => [
                'intersect' => false,
                'mode' => 'index',
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Time Period',
                        'font' => [
                            'size' => 18, // Adjust font size
                        ],
                    ],
                    'ticks' => [
                        'maxRotation' => 45, // Rotate labels if they're crowded
                        'minRotation' => 45,
                        'autoSkip' => true,
                        'maxTicksLimit' => 12, // Control number of ticks
                    ],
                ],
                'y' => [
                    'display' => true,
//                    'height' => '500px',
                    'title' => [
                        'display' => true,
                        'text' => 'Number of Users',
                        'font' => [
                            'size' => 18,
                        ],
                    ],
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1, // Control interval between ticks
                    ],
                ],
            ],
        ];
    }

    // Optional: Add polling to refresh data automatically
    protected static ?string $pollingInterval = '30s';

    // Optional: Add caching for better performance
    protected function getCachedData(): array
    {
        $cacheKey = 'users_chart_' . $this->filter;

        return cache()->remember($cacheKey, now()->addMinutes(5), function () {
            return $this->getData();
        });
    }
    protected function getType(): string
    {
        return 'line';
    }

//    protected function getType(): string
//    {
//        return 'bar';
//    }
}
