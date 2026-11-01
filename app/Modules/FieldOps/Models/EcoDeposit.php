<?php

namespace App\Modules\FieldOps\Models;

use App\Models\User;
use App\Modules\Simaksi\Models\SimaksiBooking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EcoDeposit extends Model
{
    protected $table = 'eco_deposits';

    protected $fillable = [
        'booking_id',
        'deposit_amount',
        'brought_plastics_count',
        'brought_cans_count',
        'brought_bottles_count',
        'returned_plastics_count',
        'returned_cans_count',
        'returned_bottles_count',
        'deposit_status',
        'checked_in_by',
        'checked_out_by',
        'refunded_at',
    ];

    protected function casts(): array
    {
        return [
            'deposit_amount' => 'decimal:2',
            'brought_plastics_count' => 'integer',
            'brought_cans_count' => 'integer',
            'brought_bottles_count' => 'integer',
            'returned_plastics_count' => 'integer',
            'returned_cans_count' => 'integer',
            'returned_bottles_count' => 'integer',
            'refunded_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(SimaksiBooking::class, 'booking_id');
    }

    public function checkinStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    public function checkoutStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_out_by');
    }

    public function getTotalBroughtTrashAttribute(): int
    {
        return $this->brought_plastics_count + $this->brought_cans_count + $this->brought_bottles_count;
    }

    public function getTotalReturnedTrashAttribute(): int
    {
        return $this->returned_plastics_count + $this->returned_cans_count + $this->returned_bottles_count;
    }
}
