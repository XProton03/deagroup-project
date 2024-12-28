<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quotation extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'quotations';
    protected $fillable = [
        'quotation_number',
        'category',
        'customers_id',
        'project_name',
        'request_date',
        'start_date',
        'end_date',
        'price',
        'completion_percentage',
        'status',
        'employees_id',
        'notes'

    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    public function customers()
    {
        return $this->belongsTo(Customer::class, 'customers_id');
    }
    public function employees()
    {
        return $this->belongsTo(Employee::class, 'employees_id');
    }
    public function tasks()
    {
        return $this->hasMany(Task::class, 'quotations_id');
    }
    public function quotation_files()
    {
        return $this->hasMany(QuotationFile::class, 'quotations_id');
    }
}
