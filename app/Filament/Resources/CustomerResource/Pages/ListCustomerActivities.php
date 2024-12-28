<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use pxlrbt\FilamentActivityLog\Pages\ListActivities;

class ListCustomerActivities extends ListActivities
{
    protected static string $resource = CustomerResource::class;
}
