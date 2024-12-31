<?php

namespace App\Filament\Widgets;

use App\Models\Quotation;
use Flowframe\Trend\Trend;
use Illuminate\Support\Carbon;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;

class QuotationChart extends ChartWidget
{
    protected static ?string $heading = 'Quotation Chart';
    protected static ?int $sort = 2;

    protected function getData(): array
    {

        $data = Trend::model(Quotation::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
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
        return 'bar';
    }
}
