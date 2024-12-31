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
        'price_tasks',
        'completion_percentage',
        'status',
        'employees_id',
        'notes'

    ];
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($quotation) {
            // Ambil data relasi payment
            $payment = $quotation->quotation_payment;

            // Logika untuk menentukan status
            if ($quotation->completion_percentage != 100.00) {
                $quotation->status = 'Open';
            } elseif ($quotation->completion_percentage == 100.00 && (!$payment || $payment->payment_number === null)) {
                $quotation->status = 'Payment Process';
            } elseif ($quotation->completion_percentage == 100.00 && $payment && $payment->payment_number !== null) {
                $quotation->status = 'Completed';
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }
    public static function generateQuotationNumber()
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;
        $romanMonths = [ // Angka Romawi untuk bulan
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];

        $latest = self::whereYear('created_at', $currentYear)
            ->orderBy('id', 'desc')
            ->value('quotation_number');

        $number = 1; // Default jika belum ada data
        if ($latest) {
            // Ekstrak bagian nomor pertama
            $parts = explode('/', $latest);
            $number = (int)$parts[0] + 1;
        }

        // Format: 00003/DGJ-Q/XII/2024
        return str_pad($number, 5, '0', STR_PAD_LEFT)
            . '/DGJ-Q/'
            . $romanMonths[$currentMonth]
            . "/$currentYear";
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
    public function quotation_payment()
    {
        return $this->hasOne(QuotationPayment::class, 'quotations_id');
    }
}
