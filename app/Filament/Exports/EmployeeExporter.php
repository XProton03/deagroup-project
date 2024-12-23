<?php

namespace App\Filament\Exports;

use App\Models\Employee;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class EmployeeExporter extends Exporter
{
    protected static ?string $model = Employee::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('employee_code'),
            ExportColumn::make('name'),
            ExportColumn::make('gender'),
            ExportColumn::make('birth_date'),
            ExportColumn::make('phone'),
            ExportColumn::make('email'),
            ExportColumn::make('address'),
            ExportColumn::make('provinces.name'),
            ExportColumn::make('regencies.name'),
            ExportColumn::make('districts.name'),
            ExportColumn::make('villages.name'),
            ExportColumn::make('departments.department_name'),
            ExportColumn::make('job_positions.position_name'),
            ExportColumn::make('employement_statuses.status_name'),
            ExportColumn::make('contract_start_date'),
            ExportColumn::make('contract_end_date'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your employee export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
