<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class District extends Model
{
    use HasFactory;

    protected $table = 'districts';
    protected $fillable = [
        'regencies_id',
        'name',
    ];
    public function regencies()
    {
        return $this->belongsTo(Regency::class, 'regencies_id');
    }
}
