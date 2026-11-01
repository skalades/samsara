<?php

namespace App\Modules\Simaksi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SimaksiMember extends Model
{
    protected $table = 'simaksi_members';

    protected $fillable = [
        'booking_id',
        'full_name',
        'nik',
        'phone',
        'gender',
        'age',
        'medical_history',
        'emergency_contact_name',
        'emergency_contact_phone',
    ];

    protected function casts(): array
    {
        return [
            'age' => 'integer',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(SimaksiBooking::class, 'booking_id');
    }
}
