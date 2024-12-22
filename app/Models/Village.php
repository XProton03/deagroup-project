<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Village extends Model
{
    use HasFactory;

    protected $table = 'villages';
    protected $fillable = [
        'districts_id',
        'name',
    ];
    public function districts()
    {
        return $this->belongsTo(District::class, 'districts_id');
    }
}
