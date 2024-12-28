<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'customers';
    protected $fillable = [
        'name',
        'email',
        'phone',
        'customer_type',
        'companies_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }
    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class, 'customers_id');
    }
    public function companies()
    {
        return $this->belongsTo(Company::class, 'companies_id');
    }
}
