<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployementStatus extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'employement_statuses';
    protected $fillable = [
        'status_name',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'employement_statuses_id');
    }
}
