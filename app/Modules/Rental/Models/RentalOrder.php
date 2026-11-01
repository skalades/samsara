<?php

namespace App\Modules\Rental\Models;

use App\Modules\Simaksi\Models\SimaksiBooking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RentalOrder extends Model
{
    protected $table = 'rental_orders';

    protected $fillable = [
        'order_code',
        'booking_id',
        'customer_name',
        'customer_phone',
        'id_card_deposit_type',
        'total_rental_fee',
        'total_deposit_amount',
        'damage_fee',
        'status',
        'rented_at',
        'returned_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'total_rental_fee' => 'decimal:2',
            'total_deposit_amount' => 'decimal:2',
            'damage_fee' => 'decimal:2',
            'rented_at' => 'datetime',
            'returned_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(SimaksiBooking::class, 'booking_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(RentalOrderItem::class, 'rental_order_id');
    }

    public function getNetRefundDepositAttribute(): float
    {
        return max(0, (float) ($this->total_deposit_amount - $this->damage_fee));
    }
}
