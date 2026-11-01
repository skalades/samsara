<?php

namespace App\Modules\Rental\Models;

use App\Shared\Enums\RentalAssetStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RentalItem extends Model
{
    protected $table = 'rental_items';

    protected $fillable = [
        'asset_code',
        'name',
        'category',
        'rental_rate_per_day',
        'deposit_rate',
        'status',
        'times_rented_count',
        'last_cleaned_at',
        'condition_notes',
    ];

    protected function casts(): array
    {
        return [
            'rental_rate_per_day' => 'decimal:2',
            'deposit_rate' => 'decimal:2',
            'status' => RentalAssetStatus::class,
            'times_rented_count' => 'integer',
            'last_cleaned_at' => 'datetime',
        ];
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(RentalOrderItem::class, 'rental_item_id');
    }

    public function isAvailable(): bool
    {
        return $this->status === RentalAssetStatus::TERSEDIA;
    }
}
