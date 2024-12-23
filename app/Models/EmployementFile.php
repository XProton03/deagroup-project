<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;

class EmployementFile extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'employement_files';
    protected $fillable = [
        'employees_id',
        'file_name',
        'file',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    public function employees()
    {
        return $this->belongsTo(Employee::class, 'employees_id');
    }
}
