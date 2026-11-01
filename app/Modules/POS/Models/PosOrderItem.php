<?php

namespace App\Modules\POS\Models;

use App\Modules\Consignment\Models\ConsignmentMitra;
use App\Shared\Enums\PosItemType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosOrderItem extends Model
{
    protected $table = 'pos_order_items';

    protected $fillable = [
        'pos_order_id',
        'item_type',
        'reference_id',
        'item_name',
        'qty',
        'unit_price',
        'subtotal',
        'cost_price',
        'mitra_id',
        'consignment_settlement_id',
    ];

    protected function casts(): array
    {
        return [
            'item_type' => PosItemType::class,
            'qty' => 'integer',
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'cost_price' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(PosOrder::class, 'pos_order_id');
    }

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(ConsignmentMitra::class, 'mitra_id');
    }

    public function settlement(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Consignment\Models\ConsignmentSettlement::class, 'consignment_settlement_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Store\Models\Product::class, 'reference_id');
    }
}
