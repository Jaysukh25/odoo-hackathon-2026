<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'type',
        'description',
        'cost',
        'odometer_at_service',
        'performed_at',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'odometer_at_service' => 'decimal:2',
        'performed_at' => 'datetime',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    protected static function booted()
    {
        static::created(function ($maintenanceLog) {
            $maintenanceLog->vehicle->update(['status' => 'in_shop']);
        });
    }
}
