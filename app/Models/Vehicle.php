<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'model',
        'license_plate',
        'max_capacity',
        'odometer',
        'status',
        'out_of_service',
    ];

    protected $casts = [
        'max_capacity' => 'decimal:2',
        'odometer' => 'decimal:2',
        'out_of_service' => 'boolean',
    ];

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    public function maintenanceLogs()
    {
        return $this->hasMany(MaintenanceLog::class);
    }

    public function fuelLogs()
    {
        return $this->hasMany(FuelLog::class);
    }

    public function currentTrip()
    {
        return $this->hasOne(Trip::class)->where('status', 'on_trip');
    }

    public function lastMaintenance()
    {
        return $this->maintenanceLogs()->latest()->first();
    }

    public function needsMaintenance()
    {
        $lastMaintenance = $this->lastMaintenance();
        if (!$lastMaintenance) {
            return $this->odometer > 5000;
        }
        return $this->odometer > $lastMaintenance->odometer_at_service + 5000;
    }

    public function getTotalFuelCostAttribute()
    {
        return $this->fuelLogs()->sum('cost');
    }

    public function getTotalMaintenanceCostAttribute()
    {
        return $this->maintenanceLogs()->sum('cost');
    }

    public function getTotalOperationalCostAttribute()
    {
        return $this->total_fuel_cost + $this->total_maintenance_cost;
    }

    public function getAverageFuelEfficiencyAttribute()
    {
        $fuelLogs = $this->fuelLogs;
        if ($fuelLogs->isEmpty()) {
            return 0;
        }

        $totalDistance = $this->trips()->where('status', 'completed')->sum('distance');
        $totalFuel = $fuelLogs->sum('liters');

        return $totalFuel > 0 ? $totalDistance / $totalFuel : 0;
    }
}
