<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'liters',
        'cost_per_liter',
        'cost',
        'odometer',
        'fuel_date',
    ];

    protected $casts = [
        'liters' => 'decimal:2',
        'cost_per_liter' => 'decimal:2',
        'cost' => 'decimal:2',
        'odometer' => 'decimal:2',
        'fuel_date' => 'datetime',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    protected static function booted()
    {
        static::creating(function ($fuelLog) {
            if (!$fuelLog->cost && $fuelLog->liters && $fuelLog->cost_per_liter) {
                $fuelLog->cost = $fuelLog->liters * $fuelLog->cost_per_liter;
            }
        });
    }
}
