<?php

namespace App\Modules\Consignment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConsignmentSettlement extends Model
{
    protected $table = 'consignment_settlements';

    protected $fillable = [
        'settlement_code',
        'consignment_mitra_id',
        'period_start',
        'period_end',
        'total_items_sold',
        'total_sales_amount',
        'mitra_earnings_amount',
        'samsara_margin_amount',
        'status',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'total_items_sold' => 'integer',
            'total_sales_amount' => 'decimal:2',
            'mitra_earnings_amount' => 'decimal:2',
            'samsara_margin_amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(ConsignmentMitra::class, 'consignment_mitra_id');
    }

    public function posOrderItems(): HasMany
    {
        return $this->hasMany(\App\Modules\POS\Models\PosOrderItem::class, 'consignment_settlement_id');
    }
}
