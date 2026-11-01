<?php

namespace App\Modules\Admin\Services;

use App\Modules\Admin\Models\WebsiteVisit;
use App\Modules\POS\Models\PosOrderItem;
use App\Modules\Simaksi\Models\SimaksiBooking;
use App\Modules\Store\Models\Product;
use App\Shared\Enums\BookingStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Tren pendaki dan jumlah rombongan per bulan (Chart.js compatible)
     */
    public function getMonthlyHikerTrends(?int $year = null): array
    {
        $year = $year ?? (int) date('Y');
        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        $labels = array_values($months);
        $hikersData = array_fill(0, 12, 0);
        $bookingsData = array_fill(0, 12, 0);

        // Ambil data booking per bulan untuk tahun terpilih
        $bookings = SimaksiBooking::whereYear('hiking_date', $year)
            ->where('status', '!=', BookingStatus::CANCELLED)
            ->get();

        foreach ($bookings as $booking) {
            $monthIndex = (int) Carbon::parse($booking->hiking_date)->format('n') - 1;
            if ($monthIndex >= 0 && $monthIndex < 12) {
                $hikersData[$monthIndex] += (int) $booking->total_hikers;
                $bookingsData[$monthIndex] += 1;
            }
        }

        $totalYearHikers = array_sum($hikersData);
        $totalYearBookings = array_sum($bookingsData);
        
        // Cari bulan puncak (peak month)
        $maxHikers = max($hikersData);
        $peakMonthIndex = $maxHikers > 0 ? array_search($maxHikers, $hikersData) : 0;
        $peakMonthName = $labels[$peakMonthIndex] ?? 'N/A';

        return [
            'year' => $year,
            'labels' => $labels,
            'hikers' => $hikersData,
            'bookings' => $bookingsData,
            'total_hikers' => $totalYearHikers,
            'total_bookings' => $totalYearBookings,
            'peak_month' => $peakMonthName,
            'peak_hikers' => $maxHikers,
        ];
    }

    /**
     * Heatmap hari paling padat: Matriks 12 Bulan x 7 Hari (Senin - Minggu)
     */
    public function getPeakDaysHeatmap(?int $year = null): array
    {
        $year = $year ?? (int) date('Y');
        
        $dayNames = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // Inisialisasi matriks: [month_num][day_iso_num] = count
        // Hari ISO: 1 (Senin) s/d 7 (Minggu)
        $matrix = [];
        for ($m = 1; $m <= 12; $m++) {
            $matrix[$m] = [];
            for ($d = 1; $d <= 7; $d++) {
                $matrix[$m][$d] = [
                    'month_name' => $monthNames[$m],
                    'day_name' => $dayNames[$d - 1],
                    'count' => 0,
                    'groups' => 0,
                    'intensity' => 0, // 0 - 5 level
                ];
            }
        }

        $bookings = SimaksiBooking::whereYear('hiking_date', $year)
            ->where('status', '!=', BookingStatus::CANCELLED)
            ->get();

        $maxCellHikers = 0;
        $dayTotals = array_fill(1, 7, 0);

        foreach ($bookings as $booking) {
            $date = Carbon::parse($booking->hiking_date);
            $m = (int) $date->format('n');
            $d = (int) $date->isoWeekday(); // 1 = Monday ... 7 = Sunday

            $matrix[$m][$d]['count'] += (int) $booking->total_hikers;
            $matrix[$m][$d]['groups'] += 1;
            $dayTotals[$d] += (int) $booking->total_hikers;

            if ($matrix[$m][$d]['count'] > $maxCellHikers) {
                $maxCellHikers = $matrix[$m][$d]['count'];
            }
        }

        // Hitung level intensitas (0 - 5) untuk visual heatmap
        foreach ($matrix as $m => $days) {
            foreach ($days as $d => $cell) {
                if ($cell['count'] === 0) {
                    $intensity = 0;
                } elseif ($maxCellHikers > 0) {
                    $ratio = $cell['count'] / $maxCellHikers;
                    if ($ratio < 0.15) {
                        $intensity = 1;
                    } elseif ($ratio < 0.35) {
                        $intensity = 2;
                    } elseif ($ratio < 0.60) {
                        $intensity = 3;
                    } elseif ($ratio < 0.85) {
                        $intensity = 4;
                    } else {
                        $intensity = 5;
                    }
                } else {
                    $intensity = 0;
                }
                $matrix[$m][$d]['intensity'] = $intensity;
            }
        }

        // Cari hari paling ramai dalam seminggu
        $busiestDayIndex = 1;
        $highestDayCount = 0;
        foreach ($dayTotals as $d => $sum) {
            if ($sum > $highestDayCount) {
                $highestDayCount = $sum;
                $busiestDayIndex = $d;
            }
        }
        $busiestDayName = $dayNames[$busiestDayIndex - 1] ?? 'Sabtu';

        return [
            'year' => $year,
            'months' => $monthNames,
            'days' => $dayNames,
            'matrix' => $matrix,
            'max_cell_hikers' => $maxCellHikers,
            'busiest_day' => $busiestDayName,
            'busiest_day_total' => $highestDayCount,
        ];
    }

    /**
     * Conversion Rate: Visitor Website -> Booking SIMAKSI & Funnel Analitik
     */
    public function getConversionRateStats(): array
    {
        $totalVisits = WebsiteVisit::count();
        // Hitung unique sessions
        $uniqueVisitors = WebsiteVisit::distinct('session_id')->count('session_id');
        if ($uniqueVisitors === 0 && $totalVisits > 0) {
            $uniqueVisitors = $totalVisits;
        }

        $bookingPageVisits = WebsiteVisit::where('page_type', 'booking')
            ->distinct('session_id')
            ->count('session_id');

        $totalBookings = SimaksiBooking::where('status', '!=', BookingStatus::CANCELLED)->count();
        $paidBookings = SimaksiBooking::whereIn('status', [
            BookingStatus::CONFIRMED,
            BookingStatus::CHECKED_IN,
            BookingStatus::ON_TRAIL,
            BookingStatus::CHECKED_OUT,
        ])->count();

        // Safe denominator
        $effectiveVisitors = max($uniqueVisitors, 1);
        $overallConversionRate = round(($totalBookings / $effectiveVisitors) * 100, 1);
        $paidConversionRate = $totalBookings > 0 ? round(($paidBookings / $totalBookings) * 100, 1) : 0;
        $formBookingConversion = $effectiveVisitors > 0 ? round(($bookingPageVisits / $effectiveVisitors) * 100, 1) : 0;

        // Funnel stages
        $funnel = [
            [
                'stage' => 'Kunjungan Website (Visitors)',
                'count' => $uniqueVisitors,
                'percentage' => 100,
                'color' => 'bg-slate-700',
                'icon' => 'fas fa-globe',
            ],
            [
                'stage' => 'Akses Form Registrasi SIMAKSI',
                'count' => max($bookingPageVisits, $totalBookings),
                'percentage' => min(100, round((max($bookingPageVisits, $totalBookings) / $effectiveVisitors) * 100, 1)),
                'color' => 'bg-sky-600',
                'icon' => 'fas fa-file-pen',
            ],
            [
                'stage' => 'Booking Dibuat (Submit Manifest)',
                'count' => $totalBookings,
                'percentage' => min(100, $overallConversionRate),
                'color' => 'bg-[#D97736]',
                'icon' => 'fas fa-ticket',
            ],
            [
                'stage' => 'Pembayaran Lunas & Terkonfirmasi',
                'count' => $paidBookings,
                'percentage' => min(100, round(($paidBookings / $effectiveVisitors) * 100, 1)),
                'color' => 'bg-emerald-600',
                'icon' => 'fas fa-circle-check',
            ],
        ];

        return [
            'total_visits' => $totalVisits,
            'unique_visitors' => $uniqueVisitors,
            'booking_page_visitors' => $bookingPageVisits,
            'total_bookings' => $totalBookings,
            'paid_bookings' => $paidBookings,
            'conversion_rate' => $overallConversionRate,
            'paid_conversion_rate' => $paidConversionRate,
            'funnel' => $funnel,
        ];
    }

    /**
     * Top produk terjual di toko (Beli Putus & Konsinyasi UMKM)
     */
    public function getTopSellingProducts(int $limit = 8): array
    {
        $sales = PosOrderItem::whereIn('item_type', ['STORE_BELI_PUTUS', 'STORE_KONSINYASI'])
            ->select(
                'item_name',
                'item_type',
                'reference_id',
                DB::raw('SUM(qty) as total_qty'),
                DB::raw('SUM(subtotal) as total_revenue'),
                DB::raw('AVG(unit_price) as avg_price')
            )
            ->groupBy('item_name', 'item_type', 'reference_id')
            ->orderByDesc('total_qty')
            ->take($limit)
            ->get();

        $allProducts = Product::all()->keyBy('id');
        $totalStoreRevenue = PosOrderItem::whereIn('item_type', ['STORE_BELI_PUTUS', 'STORE_KONSINYASI'])->sum('subtotal') ?: 1;

        $topProducts = [];
        $rank = 1;

        foreach ($sales as $sale) {
            $product = $sale->reference_id ? ($allProducts[$sale->reference_id] ?? null) : null;
            $stock = $product ? $product->stock : 0;
            $minAlert = $product ? $product->minimum_stock_alert : 5;
            $isConsignment = ($sale->item_type instanceof \App\Shared\Enums\PosItemType ? $sale->item_type->value : (string) $sale->item_type) === 'STORE_KONSINYASI';
            $category = $product ? $product->category->label() : ($isConsignment ? 'UMKM Lokal' : 'Logistik');

            $topProducts[] = [
                'rank' => $rank++,
                'name' => $sale->item_name,
                'type' => $isConsignment ? 'Titip Jual UMKM' : 'Beli Putus SAMSARA',
                'category' => $category,
                'total_qty' => (int) $sale->total_qty,
                'total_revenue' => (float) $sale->total_revenue,
                'avg_price' => (float) $sale->avg_price,
                'current_stock' => $stock,
                'is_low_stock' => $stock <= $minAlert,
                'revenue_share_percent' => round(($sale->total_revenue / $totalStoreRevenue) * 100, 1),
            ];
        }

        return $topProducts;
    }
}
