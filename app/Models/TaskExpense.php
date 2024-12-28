<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskExpense extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'task_expenses';
    protected $fillable = [
        'tasks_id',
        'description',
        'ammount',
        'type',
        'file',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }
    public function tasks()
    {
        return $this->belongsTo(Task::class, 'tasks_id');
    }
}
