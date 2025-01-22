<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TaskTable extends BaseWidget
{

    protected static ?string $heading = 'Task Progress';
    protected static ?int $sort = 5;
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Task::query()->where('status', '!=', 'Completed')
            )
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('task_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('companies.company_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('companies.villages.districts.regencies.name')
                    ->label('Location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('employees.name')
                    ->label('Engineer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->badge()
                    ->color(fn($state) => [
                        'Planing' => 'primary',
                        'In Progress' => 'warning',
                        'Document Progress' => 'info',
                        'Completed' => 'success',
                        'Cancel' => 'danger',
                    ][$state] ?? 'secondary'),
            ]);
    }
}
