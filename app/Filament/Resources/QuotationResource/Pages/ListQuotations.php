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
            'Completed' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', '=', 'Completed'))
                ->badge(Quotation::query()->where('status', '=', 'Completed')->count()),
            'Open' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', '=', 'Open'))
                ->badge(Quotation::query()->where('status', '=', 'Open')->count()),
            'Payment Process' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', '=', 'Payment Process'))
                ->badge(Quotation::query()->where('status', '=', 'Payment Process')->count()),
        ];
    }
}
