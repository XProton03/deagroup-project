<?php

namespace App\Filament\Exports;

use App\Models\Task;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TaskExporter extends Exporter
{
    protected static ?string $model = Task::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('quotations.quotation_number')
                ->label('Quotation Number'),
            ExportColumn::make('task_number'),
            ExportColumn::make('companies.company_name')
                ->label('Company'),
            ExportColumn::make('companies.villages.name')
                ->label('Location'),
            ExportColumn::make('pic'),
            ExportColumn::make('phone'),
            ExportColumn::make('short_description')
                ->formatStateUsing(fn($state) => strip_tags($state)),
            ExportColumn::make('job_description')
                ->formatStateUsing(fn($state) => strip_tags($state)),
            ExportColumn::make('schedule'),
            ExportColumn::make('start_date'),
            ExportColumn::make('end_date'),
            ExportColumn::make('duration'),
            ExportColumn::make('employees.name')
                ->label('Engineer'),
            ExportColumn::make('status'),
            ExportColumn::make('job_costs.mandays')
                ->label('Mandays')
                ->formatStateUsing(fn($state) => 'IDR ' . number_format($state, 2, ',', '.')),
            ExportColumn::make('job_costs.transports')
                ->label('Transports')
                ->formatStateUsing(fn($state) => 'IDR ' . number_format($state, 2, ',', '.')),
            ExportColumn::make('job_costs.accomodations')
                ->label('Accomodations')
                ->formatStateUsing(fn($state) => 'IDR ' . number_format($state, 2, ',', '.')),
            ExportColumn::make('notes')
                ->formatStateUsing(fn($state) => strip_tags($state)),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your task export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
