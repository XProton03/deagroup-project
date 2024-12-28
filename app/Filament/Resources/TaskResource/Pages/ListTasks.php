<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use App\Models\Task;
use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

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
                ->badge(Task::query()->count()),
            'Planing' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', '=', 'Planing'))
                ->badge(Task::query()->where('status', '=', 'Planing')->count()),
            'In Progress' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', '=', 'In Progress'))
                ->badge(Task::query()->where('status', '=', 'In Progress')->count()),
            'Completed' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', '=', 'Completed'))
                ->badge(Task::query()->where('status', '=', 'Completed')->count()),
            'Cancel' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', '=', 'Cancel'))
                ->badge(Task::query()->where('status', '=', 'Cancel')->count()),
        ];
    }
}
