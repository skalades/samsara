<?php

namespace App\Modules\Consignment\Services;

use App\Modules\Consignment\Models\ConsignmentMitra;
use App\Modules\Consignment\Models\ConsignmentSettlement;
use App\Modules\POS\Models\PosOrderItem;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ConsignmentService
{
    public function generateSettlementForMitra(int $mitraId, Carbon $startDate, Carbon $endDate, ?string $notes = null): ?ConsignmentSettlement
    {
        $mitra = ConsignmentMitra::findOrFail($mitraId);

        return DB::transaction(function () use ($mitra, $startDate, $endDate, $notes) {
            // Find sold items for this mitra within date range that have not been settled and are PAID
            $items = PosOrderItem::where('mitra_id', $mitra->id)
                ->whereNull('consignment_settlement_id')
                ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->whereHas('order', fn ($q) => $q->where('payment_status', 'PAID'))
                ->lockForUpdate()
                ->get();

            if ($items->isEmpty()) {
                return null;
            }

            $totalQty = $items->sum('qty');
            $totalSales = $items->sum('subtotal');
            // cost_price is what is owed to mitra
            $totalMitraEarnings = $items->sum(fn ($i) => $i->cost_price * $i->qty);
            $samsaraMargin = max(0, $totalSales - $totalMitraEarnings);

            $code = 'SETTLE-' . $endDate->format('Ym') . '-' . Str::upper(Str::random(4));

            $settlement = ConsignmentSettlement::create([
                'settlement_code' => $code,
                'consignment_mitra_id' => $mitra->id,
                'period_start' => $startDate->toDateString(),
                'period_end' => $endDate->toDateString(),
                'total_items_sold' => $totalQty,
                'total_sales_amount' => $totalSales,
                'mitra_earnings_amount' => $totalMitraEarnings,
                'samsara_margin_amount' => $samsaraMargin,
                'status' => 'DRAFT',
                'paid_at' => null,
                'notes' => $notes ?? 'Auto-settlement periodik UMKM Warga Cigedug',
            ]);

            // Mark items as settled to prevent double settlement
            PosOrderItem::whereIn('id', $items->pluck('id'))->update([
                'consignment_settlement_id' => $settlement->id,
            ]);

            return $settlement;
        });
    }

    public function generateAllPendingSettlements(Carbon $startDate, Carbon $endDate): Collection
    {
        $mitras = ConsignmentMitra::where('active', true)->get();
        $settlements = collect();

        foreach ($mitras as $mitra) {
            $settlement = $this->generateSettlementForMitra($mitra->id, $startDate, $endDate);
            if ($settlement) {
                $settlements->push($settlement);
            }
        }

        return $settlements;
    }

    /**
     * Konfirmasi settlement DRAFT -> PAID setelah transfer berhasil dilakukan ke rekening mitra.
     */
    public function confirmSettlement(int $settlementId): ConsignmentSettlement
    {
        $settlement = ConsignmentSettlement::with('mitra')->findOrFail($settlementId);

        if ($settlement->status === 'PAID') {
            throw new \RuntimeException("Settlement {$settlement->settlement_code} sudah berstatus LUNAS DITRANSFER.");
        }

        $settlement->update([
            'status' => 'PAID',
            'paid_at' => now(),
        ]);

        return $settlement->fresh(['mitra']);
    }

    public function generateWhatsAppSlip(ConsignmentSettlement $settlement): string
    {
        $mitra = $settlement->mitra;
        $period = Carbon::parse($settlement->period_start)->format('d M') . ' s/d ' . Carbon::parse($settlement->period_end)->format('d M Y');
        $statusText = $settlement->status === 'PAID' ? 'LUNAS DITRANSFER' : 'MENUNGGU TRANSFER (DRAFT)';
        $paidDateText = $settlement->paid_at ? $settlement->paid_at->format('d/m/Y H:i') . ' WIB' : 'Menunggu Konfirmasi Transfer';

        return "🌿 *SLIP REKONSILIASI KONSINYASI SAMSARA BASECAMP* 🌿\n"
            . "----------------------------------------\n"
            . "Kode Settlement: *{$settlement->settlement_code}*\n"
            . "Mitra UMKM: *{$mitra->name}*\n"
            . "Lokasi: *{$mitra->village_address}*\n"
            . "Periode: *{$period}*\n"
            . "----------------------------------------\n"
            . "📦 Total Barang Terjual: *{$settlement->total_items_sold} unit*\n"
            . "💰 Total Omset Penjualan: *Rp " . number_format($settlement->total_sales_amount, 0, ',', '.') . "*\n"
            . "📈 Margin Kas SAMSARA: *Rp " . number_format($settlement->samsara_margin_amount, 0, ',', '.') . "*\n"
            . "----------------------------------------\n"
            . "✅ *HAK PEMBAYARAN MITRA: Rp " . number_format($settlement->mitra_earnings_amount, 0, ',', '.') . "*\n"
            . "Rekening/E-Wallet: *{$mitra->bank_info}*\n"
            . "Status: *{$statusText}*\n"
            . "Tanggal Proses: *{$paidDateText}*\n"
            . "----------------------------------------\n"
            . "Hatur nuhun atas kemitraan dan dukungannya untuk kemajuan bersama warga Cigedug! 🙏🏔️";
    }
}
