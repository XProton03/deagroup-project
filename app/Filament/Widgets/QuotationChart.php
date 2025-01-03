<?php

namespace App\Filament\Widgets;

use App\Models\Quotation;
use Flowframe\Trend\Trend;
use Illuminate\Support\Carbon;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class QuotationChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Quotation Chart';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $filters = $this->getFilters();
        $startDate = $filters['startDate'] ?? now()->startOfYear();
        $endDate = $filters['endDate'] ?? now()->endOfYear();
        $data = Trend::model(Quotation::class)
            ->between(
                start: Carbon::parse($startDate),
                end: Carbon::parse($endDate),
            )
            ->perMonth('request_date')
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Quotation request from partners',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => Carbon::parse($value->date)->format('M')),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
