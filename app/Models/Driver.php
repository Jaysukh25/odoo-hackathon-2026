<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'license_number',
        'license_expiry',
        'phone',
        'status',
    ];

    protected $casts = [
        'license_expiry' => 'date',
    ];

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    public function currentTrip()
    {
        return $this->hasOne(Trip::class)->where('status', 'on_trip');
    }

    public function driverScores()
    {
        return $this->hasMany(DriverScore::class);
    }

    public function getLicenseExpiringSoonAttribute()
    {
        return $this->license_expiry->diffInDays(now()) <= 30;
    }

    public function getLicenseExpiredAttribute()
    {
        return $this->license_expiry->isPast();
    }

    public function getRiskScoreAttribute()
    {
        $lateTrips = $this->trips()->where('status', 'completed')->where('arrived_late', true)->count();
        $cancelledTrips = $this->trips()->where('status', 'cancelled')->count();
        $licenseWarning = $this->license_expiring_soon ? 1 : 0;

        return ($lateTrips * 2) + ($cancelledTrips * 3) + ($licenseWarning * 5);
    }

    public function getRiskLevelAttribute()
    {
        $score = $this->risk_score;
        
        if ($score <= 5) {
            return 'SAFE';
        } elseif ($score <= 15) {
            return 'MODERATE';
        } else {
            return 'RISKY';
        }
    }

    public function getRiskColorAttribute()
    {
        return match($this->risk_level) {
            'SAFE' => 'success',
            'MODERATE' => 'warning',
            'RISKY' => 'danger',
            default => 'secondary',
        };
    }

    public function canBeAssigned()
    {
        return !$this->license_expired && !$this->currentTrip && $this->status !== 'on_duty';
    }
}
