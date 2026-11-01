<?php

namespace App\Modules\Rental\Services;

use App\Modules\Rental\Models\RentalItem;
use App\Modules\Rental\Models\RentalOrder;
use App\Modules\Rental\Models\RentalOrderItem;
use App\Shared\Enums\RentalAssetStatus;
use App\Shared\Enums\RentalCondition;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RentalService
{
    public function getKanbanAssets(): array
    {
        $assets = RentalItem::orderBy('asset_code')->get();

        return [
            'TERSEDIA' => $assets->where('status', RentalAssetStatus::TERSEDIA)->values(),
            'DISEWA' => $assets->where('status', RentalAssetStatus::DISEWA)->values(),
            'PERLU_QC_CUCI' => $assets->where('status', RentalAssetStatus::PERLU_QC_CUCI)->values(),
            'DALAM_PERBAIKAN' => $assets->where('status', RentalAssetStatus::DALAM_PERBAIKAN)->values(),
        ];
    }

    public function updateAssetStatus(int $assetId, RentalAssetStatus $status, ?string $notes = null): RentalItem
    {
        $item = RentalItem::findOrFail($assetId);
        $data = ['status' => $status];

        if ($notes !== null) {
            $data['condition_notes'] = $notes;
        }

        if ($status === RentalAssetStatus::TERSEDIA) {
            $data['last_cleaned_at'] = now();
        }

        $item->update($data);

        return $item;
    }

    public function createRentalOrder(array $data, array $items): RentalOrder
    {
        return DB::transaction(function () use ($data, $items) {
            $totalFee = 0;
            $totalDeposit = 0;
            $orderCode = 'RNT-' . now()->format('ymd') . '-' . Str::upper(Str::random(4));

            $order = RentalOrder::create([
                'order_code' => $orderCode,
                'booking_id' => $data['booking_id'] ?? null,
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'id_card_deposit_type' => $data['id_card_deposit_type'] ?? 'KTP',
                'total_rental_fee' => 0,
                'total_deposit_amount' => 0,
                'status' => 'ACTIVE',
                'rented_at' => now(),
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($items as $itemData) {
                $rentalItem = RentalItem::lockForUpdate()->findOrFail($itemData['rental_item_id']);
                if ($rentalItem->status !== RentalAssetStatus::TERSEDIA) {
                    throw new Exception("Barang sewa {$rentalItem->name} ({$rentalItem->asset_code}) saat ini tidak tersedia.");
                }

                $days = $itemData['rental_days'] ?? 1;
                $fee = $rentalItem->rental_rate_per_day * $days;
                $deposit = $rentalItem->deposit_rate;

                $totalFee += $fee;
                $totalDeposit += $deposit;

                RentalOrderItem::create([
                    'rental_order_id' => $order->id,
                    'rental_item_id' => $rentalItem->id,
                    'rental_days' => $days,
                    'rate_per_day' => $rentalItem->rental_rate_per_day,
                    'item_deposit' => $deposit,
                    'return_condition' => RentalCondition::BAIK,
                    'damage_penalty' => 0,
                ]);

                // Mark item as rented
                $rentalItem->update([
                    'status' => RentalAssetStatus::DISEWA,
                    'times_rented_count' => $rentalItem->times_rented_count + 1,
                ]);
            }

            $order->update([
                'total_rental_fee' => $totalFee,
                'total_deposit_amount' => $totalDeposit,
            ]);

            return $order->fresh(['items.item']);
        });
    }

    public function processReturnQC(int $orderId, array $inspections): RentalOrder
    {
        return DB::transaction(function () use ($orderId, $inspections) {
            $order = RentalOrder::with('items.item')->findOrFail($orderId);
            $totalDamagePenalty = 0;

            foreach ($inspections as $itemId => $insp) {
                $orderItem = $order->items->firstWhere('id', $itemId);
                if (! $orderItem) {
                    continue;
                }

                $condition = RentalCondition::from($insp['condition']);
                $penalty = (float) ($insp['penalty'] ?? 0);
                $totalDamagePenalty += $penalty;

                $orderItem->update([
                    'return_condition' => $condition,
                    'damage_penalty' => $penalty,
                ]);

                $rentalItem = $orderItem->item;
                if ($rentalItem) {
                    $nextStatus = match ($condition) {
                        RentalCondition::BAIK => RentalAssetStatus::PERLU_QC_CUCI,
                        RentalCondition::RUSAK_RINGAN, RentalCondition::RUSAK_BERAT => RentalAssetStatus::DALAM_PERBAIKAN,
                        RentalCondition::HILANG => RentalAssetStatus::DALAM_PERBAIKAN,
                    };

                    $rentalItem->update([
                        'status' => $nextStatus,
                        'condition_notes' => $insp['notes'] ?? $condition->label(),
                    ]);
                }
            }

            $status = $totalDamagePenalty > 0 ? 'RETURNED_WITH_DAMAGE' : 'RETURNED_GOOD';

            $order->update([
                'damage_fee' => $totalDamagePenalty,
                'status' => $status,
                'returned_at' => now(),
            ]);

            return $order->fresh(['items.item']);
        });
    }
}
