<?php

namespace App\Modules\Store\Models;

use App\Modules\Consignment\Models\ConsignmentMitra;
use App\Shared\Enums\InventoryType;
use App\Shared\Enums\ProductCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'sku',
        'name',
        'category',
        'inventory_type',
        'consignment_mitra_id',
        'cost_price',
        'selling_price',
        'stock',
        'minimum_stock_alert',
        'is_preorder_available',
        'image_url',
        'active',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'category' => ProductCategory::class,
            'inventory_type' => InventoryType::class,
            'cost_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'stock' => 'integer',
            'minimum_stock_alert' => 'integer',
            'is_preorder_available' => 'boolean',
            'active' => 'boolean',
        ];
    }

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(ConsignmentMitra::class, 'consignment_mitra_id');
    }

    public function stockLogs(): HasMany
    {
        return $this->hasMany(ProductStockLog::class, 'product_id')->latest();
    }

    public function isConsignment(): bool
    {
        return $this->inventory_type === InventoryType::KONSINYASI;
    }

    public function isLowStock(): bool
    {
        return $this->stock <= $this->minimum_stock_alert;
    }

    public function getMarginAmountAttribute(): float
    {
        return (float) ($this->selling_price - $this->cost_price);
    }
}
