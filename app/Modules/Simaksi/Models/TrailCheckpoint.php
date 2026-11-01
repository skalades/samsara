<?php

namespace App\Modules\Simaksi\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrailCheckpoint extends Model
{
    protected $table = 'trail_checkpoints';

    protected $fillable = [
        'booking_id',
        'pos_name',
        'scanned_by_user_id',
        'direction',
        'hikers_passed_count',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'hikers_passed_count' => 'integer',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(SimaksiBooking::class, 'booking_id');
    }

    public function scanner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by_user_id');
    }
}
