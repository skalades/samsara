<?php

namespace App\Modules\Ojek\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OjekDriver extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_number',
        'name',
        'phone',
        'license_plate',
        'motorcycle_model',
        'community_name',
        'is_active',
        'total_trips',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'total_trips' => 'integer',
    ];

    public function attendances(): HasMany
    {
        return $this->hasMany(OjekDriverAttendance::class, 'ojek_driver_id');
    }

    public function todayAttendance(): HasOne
    {
        return $this->hasOne(OjekDriverAttendance::class, 'ojek_driver_id')
            ->whereDate('date', today());
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(OjekQueueTicket::class, 'assigned_driver_id');
    }
}
