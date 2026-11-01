<?php

namespace App\Modules\Simaksi\Models;

use App\Shared\Enums\SosType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SosAlert extends Model
{
    protected $table = 'sos_alerts';

    protected $fillable = [
        'booking_id',
        'reporter_name',
        'reporter_phone',
        'pos_location',
        'alert_type',
        'description',
        'status',
        'evacuation_team_notes',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'alert_type' => SosType::class,
            'resolved_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(SimaksiBooking::class, 'booking_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'ACTIVE';
    }
}
