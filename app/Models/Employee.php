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

    // public static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($employee) {
    //         // Ambil kode departemen dari tabel departments
    //         $department = Department::find($employee->departments_id);

    //         // Hitung jumlah karyawan di departemen tersebut
    //         $employeeCount = Employee::where('departments_id', $employee->departments_id)->count() + 1;

    //         // Format MMYY
    //         //$monthYear = now()->format('my');
    //         $monthYear = \Carbon\Carbon::parse($employee->contract_start_date)->format('my');

    //         // Generate employment_code
    //         $employee->employee_code = sprintf('%05d-%s-%s', $employeeCount, $department->department_code, $monthYear);
    //     });
    // }
    public static function boot()
    {
        parent::boot();

        // Generate NIP saat karyawan baru diinput
        static::creating(function ($employee) {
            $employee->employee_code = self::generateEmployeeCode($employee);
        });

        // Periksa jika status karyawan berubah menjadi karyawan tetap
        static::updating(function ($employee) {
            // Jika status berubah menjadi karyawan tetap, regenerate employee_code
            if ($employee->isDirty('employement_statuses_id') && $employee->employement_statuses_id == 1) {
                $employee->employee_code = self::generateEmployeeCode($employee);
            }
        });
    }
    private static function generateEmployeeCode($employee)
    {
        if ($employee->employement_statuses_id == 1) {
            // Ambil kode departemen dari tabel departments
            $department = Department::find($employee->departments_id);

            // Hitung jumlah karyawan tetap di departemen tersebut
            $employeeCount = Employee::where('departments_id', $employee->departments_id)
                ->where('employement_statuses_id', 1)
                ->count() + 1;

            // Format MMYY dari contract_start_date
            $monthYear = \Carbon\Carbon::parse($employee->contract_start_date)->format('my');

            // Generate employee_code untuk karyawan tetap
            return sprintf('%05d-%s-%s', $employeeCount, $department->department_code, $monthYear);
        } else {
            // Jika status bukan karyawan tetap, hanya gunakan angka yang bertambah
            $employeeCount = Employee::where('employement_statuses_id', '!=', 1)->count() + 1;

            // Generate employee_code untuk karyawan tidak tetap
            return sprintf('%06d', $employeeCount);
        }
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
