<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use App\Models\Quotation;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsAdminOverview extends BaseWidget
{
    protected static ?int $sort = 2;
    use InteractsWithPageFilters;
    protected function getStats(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        return [
            Stat::make('Projects', Quotation::query()->when($startDate, fn(Builder $query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn(Builder $query) => $query->whereDate('created_at', '<=', $endDate))
                ->count())
                ->description('Total projek')
                ->descriptionIcon('heroicon-m-clipboard')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
            Stat::make('Tasks', Task::query()->when($startDate, fn(Builder $query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn(Builder $query) => $query->whereDate('created_at', '<=', $endDate))->count())
                ->description('Total task dari projek')
                ->descriptionIcon('heroicon-m-archive-box')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('warning'),
            Stat::make('Price', 'IDR ' . number_format(Quotation::query()->when($startDate, fn(Builder $query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn(Builder $query) => $query->whereDate('created_at', '<=', $endDate))->where('status', 'Completed')->sum('price'), 0, ',', '.'))
                ->description('Total harga dari projek')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('success'),
        ];
    }
}
