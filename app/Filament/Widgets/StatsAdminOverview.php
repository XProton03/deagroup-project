<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use App\Models\Quotation;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsAdminOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected function getStats(): array
    {
        return [
            Stat::make('Projects', Quotation::count())
                ->description('Total projek')
                ->descriptionIcon('heroicon-m-clipboard')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
            Stat::make('Tasks', Task::count())
                ->description('Total task dari projek')
                ->descriptionIcon('heroicon-m-archive-box')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('warning'),
            Stat::make('Price', 'IDR ' . number_format(Quotation::where('status', 'Completed')->sum('price'), 0, ',', '.'))
                ->description('Total harga dari projek')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('success'),
        ];
    }
}
