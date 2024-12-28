<?php

namespace App\Filament\Resources\QuotationResource\Pages;

use Filament\Actions;
use App\Models\Quotation;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\QuotationResource;

class ListQuotations extends ListRecords
{
    protected static string $resource = QuotationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public function getTabs(): array
    {
        return [
            'All' => Tab::make()
                ->badge(Quotation::query()->count()),
            'Selesai' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', '=', 'Selesai'))
                ->badge(Quotation::query()->where('status', '=', 'Selesai')->count()),
            'Belum Selesai' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', '=', 'Belum Selesai'))
                ->badge(Quotation::query()->where('status', '=', 'Belum Selesai')->count()),
        ];
    }
}
