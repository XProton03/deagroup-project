<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Regency extends Model
{
    use HasFactory;

    protected $table = 'regencies';
    protected $fillable = [
        'provinces_id',
        'name',
    ];
    public function provinces()
    {
        return $this->belongsTo(Province::class, 'provinces_id');
    }
}
