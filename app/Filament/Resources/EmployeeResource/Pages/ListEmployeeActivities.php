<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use pxlrbt\FilamentActivityLog\Pages\ListActivities;

class ListEmployeeActivities extends ListActivities
{
    protected static string $resource = EmployeeResource::class;
}
