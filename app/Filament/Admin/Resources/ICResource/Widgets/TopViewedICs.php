<?php

namespace App\Filament\Admin\Resources\ICResource\Widgets;

use App\Models\IC;
use Filament\Tables;
use Filament\Tables\Table;
use EightyNine\FilamentAdvancedWidget\AdvancedTableWidget as BaseWidget;
use App\Models\User;
use Carbon\Carbon;
use EightyNine\FilamentAdvancedWidget\AdvancedWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\DateRangeFilter;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Colors\Color;
use Illuminate\Database\Eloquent\Builder;

class TopViewedICs extends BaseWidget
{
    use Tables\Concerns\InteractsWithTable;


//    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '200px';
    public ?int $limit = 10;
    protected int|string|array $columnSpan = '4';




//    public function table(Table $table): Table
//    {
//        return $table
//            ->query(
//                 IC::query()->orderBy('views', 'DESC')->limit(5)
//            )
//            ->columns([
//                TextColumn::make('name')->label('Name')->searchable(),
//                TextColumn::make('views')->label('Views')->searchable(),
//            ]);
//    }
//    protected function getStats(): array
//    {
//        $lastMonth = now()->subMonth();
//
//        return [
//            \EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat::make("Total Users", User::count())
//                ->icon('heroicon-s-users')
//                ->chart([10, 15, 20, 18, 25, 30])
//                ->description('All registered users')
//                ->descriptionColor('primary')
//                ->chartColor('primary')
//                ->iconColor('primary'),
//
//            \EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat::make("New This Month", User::where('created_at', '>=', $lastMonth)->count())
//                ->icon('heroicon-s-user-plus')
//                ->description('Users joined in last 30 days')
//                ->descriptionColor('success')
//                ->chartColor('success')
//                ->iconColor('success'),
//        ];
//    }
//


    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns($this->getTableColumns())
            ->heading($this->getTableHeading())
            ->description($this->getTableDescription())
            ->defaultSort('views', 'desc')
            ->paginated([7, 10])
            ->defaultPaginationPageOption(5)
            ->poll('5m') // Auto-refresh table data
            ->emptyStateHeading('No ICs Found')
            ->emptyStateDescription('There are no ICs with views to display.')
            ->emptyStateIcon('heroicon-o-eye-slash');
    }


    protected function getTableQuery(): Builder
    {
        $query = IC::query();

        return $query->where('views', '>', 0)
            ->orderBy('views', 'DESC')
            ->limit($this->limit);
    }

    protected function getTableColumns(): array
    {
        return [
            // Ranking column
            TextColumn::make('rank')
                ->label('🏆')
                ->icon('heroicon-o-trophy')
                ->rowIndex()
                ->alignCenter()
                ->size(TextColumn\TextColumnSize::Large)
                ->weight(FontWeight::Bold)
                ->color(Color::Amber),

            // Name column with enhanced styling
            TextColumn::make('name')
                ->label('IC Name')
                ->sortable()
                ->weight(FontWeight::SemiBold)
                ->color(Color::Blue),

            // Views column with badge styling
            TextColumn::make('views')
                ->label('Views')
                ->sortable()
                ->alignCenter()
                ->weight(FontWeight::Bold)
                ->suffix(' views'),
        ];
    }



    // Widget display customization
    public function getTableHeading(): string
    {
        return '🏆 Most Viewed ICs';
    }

    public function getTableDescription(): ?string
    {
        $totalViews = IC::sum('views');

        return "The Most Viewed ICs • Total: " . number_format($totalViews) . " views •";
    }


    // Additional widget properties for the advanced widget
//    protected function getStats(): array
//    {
//        $totalICs = IC::count();
//        $totalViews = IC::sum('views');
//        $avgViews = $totalICs > 0 ? round($totalViews / $totalICs) : 0;
//        $topIC = IC::orderBy('views', 'desc')->first();
//
//        return [
//            \EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat::make("Total ICs", number_format($totalICs))
//                ->icon('heroicon-s-document-text')
//                ->description('Total registered ICs')
//                ->chartColor('primary')
//                ->iconColor('primary'),
//
//            \EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat::make("Total Views", number_format($totalViews))
//                ->icon('heroicon-s-eye')
//                ->description('All time views')
//
//                ->chartColor('success')
//                ->iconColor('success'),
//
//            \EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat::make("Average Views", number_format($avgViews))
//                ->icon('heroicon-s-chart-bar')
//                ->description('Views per IC')
//                ->chartColor('warning')
//                ->iconColor('warning'),
//
//            \EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat::make("Top Performer", $topIC?->name ?? 'N/A')
//                ->icon('heroicon-s-trophy')
//                ->description($topIC ? number_format($topIC->views) . ' views' : 'No data')
////                ->descriptionColor('info')
//                ->chartColor('info')
//                ->iconColor('info'),
//        ];
//    }


}
