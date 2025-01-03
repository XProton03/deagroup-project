<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Quotation;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Widgets\TableWidget as BaseWidget;
use IbrahimBougaoua\FilaProgress\Tables\Columns\CircleProgress;

class QuotationTable extends BaseWidget
{
    protected static ?int $sort = 4;
    protected static ?string $heading = 'Quotation Progress';
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Quotation::query()->where('status', '!=', 'Completed')
            )
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('quotation_number'),
                CircleProgress::make('completion_percentage')
                    ->label('Progress'),
                Tables\Columns\TextColumn::make('price')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => [
                        'Open'              => 'primary',
                        'Payment Process'   => 'warning',
                        'Completed'         => 'success',
                    ][$state] ?? 'secondary'),
            ]);
    }
}
