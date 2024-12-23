<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'employees';
    protected $fillable = [
        'departments_id',
        'provinces_id',
        'regencies_id',
        'districts_id',
        'villages_id',
        'employee_code',
        'name',
        'gender',
        'birth_date',
        'phone',
        'email',
        'address',
        'contract_start_date',
        'contract_end_date',
        'employement_statuses_id',
        'job_positions_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    public function employement_statuses(): BelongsTo
    {
        return $this->belongsTo(EmployementStatus::class, 'employement_statuses_id');
    }
    public function job_positions(): BelongsTo
    {
        return $this->belongsTo(JobPosition::class, 'job_positions_id');
    }
    public function departments(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'departments_id');
    }
    public function provinces(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'provinces_id');
    }
    public function regencies(): BelongsTo
    {
        return $this->belongsTo(Regency::class, 'regencies_id');
    }
    public function districts(): BelongsTo
    {
        return $this->belongsTo(District::class, 'districts_id');
    }
    public function villages(): BelongsTo
    {
        return $this->belongsTo(Village::class, 'villages_id');
    }
    public function employement_files(): HasMany
    {
        return $this->hasMany(EmployementFile::class, 'employees_id');
    }
}
