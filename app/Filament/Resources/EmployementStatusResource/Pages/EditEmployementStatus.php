<?php

namespace App\Filament\Resources\EmployementStatusResource\Pages;

use App\Filament\Resources\EmployementStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmployementStatus extends EditRecord
{
    protected static string $resource = EmployementStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
