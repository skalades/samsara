<?php

namespace App\Modules\Ojek\Models;

use App\Shared\Enums\OjekDriverStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OjekDriverAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'ojek_driver_id',
        'date',
        'queue_order',
        'status',
        'checked_in_at',
        'last_called_at',
        'last_trip_completed_at',
        'daily_trips_count',
    ];

    protected $casts = [
        'date' => 'date',
        'queue_order' => 'integer',
        'status' => OjekDriverStatus::class,
        'checked_in_at' => 'datetime',
        'last_called_at' => 'datetime',
        'last_trip_completed_at' => 'datetime',
        'daily_trips_count' => 'integer',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(OjekDriver::class, 'ojek_driver_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(OjekQueueTicket::class, 'assigned_attendance_id');
    }
}
