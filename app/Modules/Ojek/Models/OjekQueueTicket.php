<?php

namespace App\Modules\Ojek\Models;

use App\Modules\POS\Models\PosOrder;
use App\Modules\Simaksi\Models\SimaksiBooking;
use App\Shared\Enums\OjekQueueStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OjekQueueTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'ojek_route_id',
        'passenger_name',
        'passenger_phone',
        'simaksi_booking_id',
        'pos_order_id',
        'source',
        'assigned_driver_id',
        'assigned_attendance_id',
        'fare_amount',
        'payment_status',
        'payment_method',
        'status',
        'called_at',
        'started_at',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'fare_amount' => 'decimal:2',
        'status' => OjekQueueStatus::class,
        'called_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function route(): BelongsTo
    {
        return $this->belongsTo(OjekRoute::class, 'ojek_route_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(OjekDriver::class, 'assigned_driver_id');
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(OjekDriverAttendance::class, 'assigned_attendance_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(SimaksiBooking::class, 'simaksi_booking_id');
    }

    public function posOrder(): BelongsTo
    {
        return $this->belongsTo(PosOrder::class, 'pos_order_id');
    }
}
