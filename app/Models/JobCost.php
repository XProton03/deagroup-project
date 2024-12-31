<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobCost extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'job_costs';
    protected $fillable = [
        'tasks_id',
        'mandays',
        'transports',
        'accomodations',
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
