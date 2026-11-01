<?php

namespace App\Modules\Admin\Services;

use App\Modules\Ojek\Models\OjekQueueTicket;
use App\Modules\POS\Models\PosOrder;
use App\Modules\POS\Models\PosOrderItem;
use App\Modules\Rental\Models\RentalItem;
use App\Modules\Rental\Models\RentalOrder;
use App\Modules\Simaksi\Models\DailyQuota;
use App\Modules\Simaksi\Models\SimaksiBooking;
use App\Modules\Simaksi\Models\SosAlert;
use App\Modules\Simaksi\Models\TrailSetting;
use App\Modules\Store\Models\Product;
use App\Shared\Enums\BookingStatus;
use App\Shared\Enums\OjekQueueStatus;
use App\Shared\Enums\TrailStatus;
use App\Shared\Enums\WeatherStatus;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AdminService
{
    public function getMountainPulse(): array
    {
        $today = today();
        $trailSetting = TrailSetting::current();

        $activeBookingsOnTrail = SimaksiBooking::whereIn('status', [BookingStatus::CHECKED_IN, BookingStatus::ON_TRAIL])
            ->get();

        $hikersOnTrailCount = $activeBookingsOnTrail->sum('total_hikers');
        $groupsOnTrailCount = $activeBookingsOnTrail->count();

        $todayQuota = DailyQuota::where('date', $today->toDateString())->first();
        $maxQuota = $todayQuota ? $todayQuota->max_quota : ($today->isWeekend() ? 300 : 150);
        $bookedCount = $todayQuota ? $todayQuota->booked_count : 0;
        $remainingQuota = $todayQuota ? $todayQuota->remaining_quota : $maxQuota;

        // Today's revenue: POS orders + confirmed direct online bookings (without POS order to avoid double counting)
        $todayPosRevenue = (float) PosOrder::whereDate('created_at', $today)->sum('total_amount');
        $todayDirectOnlineBookings = (float) SimaksiBooking::whereDate('paid_at', $today)
            ->where('status', '!=', BookingStatus::CANCELLED)
            ->whereDoesntHave('posOrders')
            ->sum('total_ticket_amount');

        $activeSosCount = SosAlert::where('status', 'ACTIVE')->count();
        $lowStockProductsCount = Product::where('active', true)
            ->whereColumn('stock', '<=', 'minimum_stock_alert')
            ->count();

        $pendingPaymentsCount = SimaksiBooking::where('status', BookingStatus::PENDING_PAYMENT)->count();

        return [
            'trail_setting' => $trailSetting,
            'hikers_on_trail' => $hikersOnTrailCount,
            'groups_on_trail' => $groupsOnTrailCount,
            'max_quota' => $maxQuota,
            'booked_count' => $bookedCount,
            'remaining_quota' => $remainingQuota,
            'today_revenue' => $todayPosRevenue + $todayDirectOnlineBookings,
            'active_sos_count' => $activeSosCount,
            'low_stock_products_count' => $lowStockProductsCount,
            'pending_payments_count' => $pendingPaymentsCount,
        ];
    }

    public function updateTrailControl(TrailStatus $status, WeatherStatus $weather, ?string $announcement = null, ?int $userId = null): TrailSetting
    {
        $setting = TrailSetting::current();
        $setting->update([
            'trail_status' => $status,
            'weather_status' => $weather,
            'announcement' => $announcement,
            'updated_by' => $userId,
        ]);

        return $setting;
    }

    public function getLivePosTracker(): array
    {
        $activeGroups = SimaksiBooking::with(['members', 'checkpoints'])
            ->whereIn('status', [BookingStatus::CHECKED_IN, BookingStatus::ON_TRAIL])
            ->get();

        $positions = [
            'Puncak Cikuray'       => [],
            'Pos 4 (Samudra Awan)' => [],
            'Pos 3 (Alam Rimba)'   => [],
            'Pos 2 (Batu Gendrung)' => [],
            'Pos 1 (Tanjakan EOD)' => [],
            'Basecamp'             => [],
        ];

        $posPatterns = [
            'Puncak Cikuray'        => ['puncak'],
            'Pos 4 (Samudra Awan)'  => ['pos 4', 'samudra'],
            'Pos 3 (Alam Rimba)'    => ['pos 3', 'alam rimba'],
            'Pos 2 (Batu Gendrung)' => ['pos 2', 'batu gendrung', 'gendrung'],
            'Pos 1 (Tanjakan EOD)'  => ['pos 1', 'tanjakan', 'eod'],
            'Basecamp'              => ['basecamp'],
        ];

        foreach ($activeGroups as $booking) {
            $lowerCurrentPos = strtolower($booking->current_pos ?? '');
            $matched = false;

            foreach ($posPatterns as $posKey => $patterns) {
                foreach ($patterns as $pattern) {
                    if (str_contains($lowerCurrentPos, $pattern)) {
                        $positions[$posKey][] = $booking;
                        $matched = true;
                        break 2;
                    }
                }
            }

            if (! $matched) {
                $positions['Basecamp'][] = $booking;
            }
        }

        return $positions;
    }

    public function getSarManifestData(int $bookingId): SimaksiBooking
    {
        return SimaksiBooking::with(['members', 'checkpoints.scanner', 'rentalOrders.items.item'])
            ->findOrFail($bookingId);
    }

    public function getFinancialReport(Carbon $start, Carbon $end): array
    {
        $posOrders = PosOrder::with('items')
            ->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()])
            ->get();

        // Direct online bookings paid in this period without POS order
        $directOnlineBookings = SimaksiBooking::whereBetween('paid_at', [$start->startOfDay(), $end->endOfDay()])
            ->where('status', '!=', BookingStatus::CANCELLED)
            ->whereDoesntHave('posOrders')
            ->get();

        $directOnlineSimaksiRevenue = $directOnlineBookings->sum('total_ticket_amount');
        $directOnlineTicketsSold = $directOnlineBookings->sum('total_hikers');

        $simaksiRevenue = $posOrders->sum('subtotal_simaksi') + $directOnlineSimaksiRevenue;
        $storeRevenue = $posOrders->sum('subtotal_store');
        $rentalRevenue = $posOrders->sum('subtotal_rental');

        // Walk-in Ojek tickets created directly at terminal without going through POS cashier
        $walkInOjekTickets = OjekQueueTicket::with('route')
            ->where('source', 'PANGKALAN_WALKIN')
            ->where('payment_status', 'PAID')
            ->whereIn('status', [OjekQueueStatus::COMPLETED, OjekQueueStatus::ON_TRIP, OjekQueueStatus::CALLED])
            ->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()])
            ->get();

        $ojekWalkInRevenue = (float) $walkInOjekTickets->sum('fare_amount');
        $ojekWalkInDriverPayout = (float) $walkInOjekTickets->sum(fn ($t) => $t->route?->driver_share ?? ($t->fare_amount * 0.8));

        $ojekRevenue = (float) $posOrders->sum('subtotal_ojek') + $ojekWalkInRevenue;
        $totalGrossRevenue = $posOrders->sum('total_amount') + $directOnlineSimaksiRevenue + $ojekWalkInRevenue;

        // Multi-Party Allocation for SIMAKSI Tickets:
        // Asuransi Pendaki: Rp 5.000 / orang
        // Retribusi Kawasan Perhutani: 35% of net ticket
        // PAD Desa Cigedug: 15% of net ticket
        // Kas SAMSARA: sisa (50% of net ticket)
        $posTicketsSold = PosOrderItem::whereIn('pos_order_id', $posOrders->pluck('id'))
            ->where('item_type', 'SIMAKSI')
            ->sum('qty');

        $totalTicketsSold = $posTicketsSold + $directOnlineTicketsSold;

        $insuranceShare = $totalTicketsSold * 5000;
        $netTicketFund = max(0, $simaksiRevenue - $insuranceShare);

        $perhutaniShare = round($netTicketFund * 0.35, 2);
        $padCigedugShare = round($netTicketFund * 0.15, 2);
        $samsaraSimaksiShare = round($netTicketFund * 0.50, 2);

        // Store & Consignment calculation
        $consignmentItems = PosOrderItem::whereIn('pos_order_id', $posOrders->pluck('id'))
            ->where('item_type', 'STORE_KONSINYASI')
            ->get();

        $consignmentMitraPayout = $consignmentItems->sum(fn ($i) => $i->cost_price * $i->qty);
        $samsaraStoreMargin = max(0, $storeRevenue - $consignmentMitraPayout);

        // Ojek Paguyuban & Driver Payout
        $ojekItems = PosOrderItem::whereIn('pos_order_id', $posOrders->pluck('id'))
            ->where('item_type', 'OJEK')
            ->get();

        $ojekDriverPayout = $ojekItems->sum(fn ($i) => $i->cost_price * $i->qty) + $ojekWalkInDriverPayout;
        $ojekPaguyubanShare = max(0, $ojekRevenue - $ojekDriverPayout);

        return [
            'period' => $start->format('d M Y') . ' - ' . $end->format('d M Y'),
            'total_gross_revenue' => $totalGrossRevenue,
            'simaksi_revenue' => $simaksiRevenue,
            'store_revenue' => $storeRevenue,
            'rental_revenue' => $rentalRevenue,
            'ojek_revenue' => $ojekRevenue,
            'total_tickets_sold' => $totalTicketsSold,
            'allocation' => [
                'samsara_kas' => $samsaraSimaksiShare + $samsaraStoreMargin + $rentalRevenue,
                'perhutani' => $perhutaniShare,
                'pad_cigedug' => $padCigedugShare,
                'asuransi' => $insuranceShare,
                'mitra_konsinyasi' => $consignmentMitraPayout,
                'driver_ojek' => $ojekDriverPayout,
                'kas_paguyuban_ojek' => $ojekPaguyubanShare,
            ],
        ];
    }
}
