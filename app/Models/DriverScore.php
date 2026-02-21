<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'score',
        'reason',
        'score_date',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'score_date' => 'datetime',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
