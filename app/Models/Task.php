<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'tasks';
    protected $fillable = [
        'quotations_id',
        'task_number',
        'companies_id',
        'pic',
        'phone',
        'short_description',
        'job_description',
        'schedule',
        'start_date',
        'end_date',
        'duration',
        'employees_id',
        'status',
        'notes',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }
    public function quotations()
    {
        return $this->belongsTo(Quotation::class, 'quotations_id', 'id');
    }
    public function companies()
    {
        return $this->belongsTo(Company::class, 'companies_id');
    }
    public function employees()
    {
        return $this->belongsTo(Employee::class, 'employees_id');
    }
    public function task_files()
    {
        return $this->hasMany(TaskFile::class, 'tasks_id');
    }
    public function task_expenses()
    {
        return $this->hasMany(TaskExpense::class, 'tasks_id');
    }
    public function job_costs()
    {
        return $this->hasMany(JobCost::class, 'tasks_id');
    }
}
