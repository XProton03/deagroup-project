<?php

namespace App\Filament\Resources\EmployementStatusResource\Pages;

use App\Filament\Resources\EmployementStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployementStatuses extends ListRecords
{
    protected static string $resource = EmployementStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
