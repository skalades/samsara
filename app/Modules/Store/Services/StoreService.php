<?php

namespace App\Modules\Store\Services;

use App\Modules\Store\Models\Product;
use App\Modules\Store\Models\ProductStockLog;
use App\Shared\Enums\InventoryType;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class StoreService
{
    public function getActiveCatalog(?string $category = null, ?string $inventoryType = null): Collection
    {
        $query = Product::with('mitra')->where('active', true);

        if ($category && $category !== 'ALL') {
            $query->where('category', $category);
        }

        if ($inventoryType && $inventoryType !== 'ALL') {
            $query->where('inventory_type', $inventoryType);
        }

        return $query->orderBy('category')->orderBy('name')->get();
    }

    public function adjustStock(int $productId, int $qtyChange, string $type, ?string $notes = null, ?int $userId = null): Product
    {
        return DB::transaction(function () use ($productId, $qtyChange, $type, $notes, $userId) {
            $product = Product::lockForUpdate()->findOrFail($productId);
            $before = $product->stock;
            $after = $before + $qtyChange;

            if ($after < 0) {
                throw new Exception("Stok untuk {$product->name} tidak mencukupi (Tersedia: {$before}, diminta: " . abs($qtyChange) . ').');
            }

            $product->update(['stock' => $after]);

            ProductStockLog::create([
                'product_id' => $product->id,
                'type' => $type,
                'qty' => $qtyChange,
                'balance_before' => $before,
                'balance_after' => $after,
                'notes' => $notes,
                'created_by' => $userId,
            ]);

            return $product;
        });
    }
}
