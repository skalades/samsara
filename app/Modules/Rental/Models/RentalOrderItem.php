<?php

namespace App\Modules\Rental\Models;

use App\Shared\Enums\RentalCondition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalOrderItem extends Model
{
    protected $table = 'rental_order_items';

    protected $fillable = [
        'rental_order_id',
        'rental_item_id',
        'rental_days',
        'rate_per_day',
        'item_deposit',
        'return_condition',
        'damage_penalty',
    ];

    protected function casts(): array
    {
        return [
            'rental_days' => 'integer',
            'rate_per_day' => 'decimal:2',
            'item_deposit' => 'decimal:2',
            'damage_penalty' => 'decimal:2',
            'return_condition' => RentalCondition::class,
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(RentalOrder::class, 'rental_order_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(RentalItem::class, 'rental_item_id');
    }
}
