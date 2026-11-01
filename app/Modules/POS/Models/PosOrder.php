<?php

namespace App\Modules\POS\Models;

use App\Models\User;
use App\Modules\Simaksi\Models\SimaksiBooking;
use App\Shared\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosOrder extends Model
{
    protected $table = 'pos_orders';

    protected $fillable = [
        'invoice_number',
        'booking_id',
        'cashier_user_id',
        'customer_name',
        'customer_phone',
        'subtotal_simaksi',
        'subtotal_store',
        'subtotal_rental',
        'subtotal_ojek',
        'eco_deposit',
        'rental_deposit',
        'total_amount',
        'paid_amount',
        'change_amount',
        'payment_method',
        'payment_status',
        'is_preorder_pickup',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal_simaksi' => 'decimal:2',
            'subtotal_store' => 'decimal:2',
            'subtotal_rental' => 'decimal:2',
            'subtotal_ojek' => 'decimal:2',
            'eco_deposit' => 'decimal:2',
            'rental_deposit' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'change_amount' => 'decimal:2',
            'payment_method' => PaymentMethod::class,
            'is_preorder_pickup' => 'boolean',
        ];
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->payment_status === 'PAID';
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_user_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(SimaksiBooking::class, 'booking_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PosOrderItem::class, 'pos_order_id');
    }
}
