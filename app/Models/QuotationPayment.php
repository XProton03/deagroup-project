<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuotationPayment extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'quotation_payments';
    protected $fillable = [
        'quotations_id',
        'users_id',
        'payment_date',
        'payment_number',
        'notes',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($payment) {
            // Ambil data quotation terkait
            $quotation = $payment->quotation;

            if ($quotation) {
                if ($quotation->completion_percentage != 100.00) {
                    $quotation->update(['status' => 'Open']);
                } elseif ($quotation->completion_percentage == 100.00 && $payment->payment_number === null) {
                    $quotation->update(['status' => 'Payment Process']);
                } elseif ($quotation->completion_percentage == 100.00 && $payment->payment_number !== null) {
                    $quotation->update(['status' => 'Completed']);
                }
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }
    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotations_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}
