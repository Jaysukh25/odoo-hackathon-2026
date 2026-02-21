<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'driver_id',
        'origin',
        'destination',
        'cargo_weight',
        'distance',
        'estimated_duration',
        'status',
        'started_at',
        'completed_at',
        'arrived_late',
    ];

    protected $casts = [
        'cargo_weight' => 'decimal:2',
        'distance' => 'decimal:2',
        'estimated_duration' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'arrived_late' => 'boolean',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(TripStatusHistory::class);
    }

    public function updateStatus($newStatus)
    {
        $oldStatus = $this->status;
        $this->status = $newStatus;
        $this->save();

        TripStatusHistory::create([
            'trip_id' => $this->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_at' => now(),
        ]);

        if ($newStatus === 'dispatched') {
            $this->vehicle->update(['status' => 'on_trip']);
            $this->driver->update(['status' => 'on_duty']);
            $this->update(['started_at' => now()]);
        } elseif ($newStatus === 'completed') {
            $this->vehicle->update(['status' => 'available']);
            $this->driver->update(['status' => 'available']);
            $this->update(['completed_at' => now()]);
            
            if ($this->estimated_duration && $this->started_at) {
                $actualDuration = $this->started_at->diffInMinutes($this->completed_at);
                $this->update(['arrived_late' => $actualDuration > $this->estimated_duration]);
            }
        } elseif ($newStatus === 'cancelled') {
            if ($this->vehicle->status === 'on_trip') {
                $this->vehicle->update(['status' => 'available']);
            }
            if ($this->driver->status === 'on_duty') {
                $this->driver->update(['status' => 'available']);
            }
        }
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'draft' => 'secondary',
            'dispatched' => 'info',
            'on_trip' => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    public function getCargoWithinCapacityAttribute()
    {
        return $this->cargo_weight <= $this->vehicle->max_capacity;
    }
}
