<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuotationFile extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'quotation_files';
    protected $fillable = [
        'quotations_id',
        'file_name',
        'file',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }
    public function quotations()
    {
        return $this->belongsTo(Quotation::class, 'quotations_id');
    }
}
