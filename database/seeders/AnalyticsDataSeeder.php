<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Admin\Models\WebsiteVisit;
use App\Modules\Consignment\Models\ConsignmentMitra;
use App\Modules\POS\Models\PosOrder;
use App\Modules\POS\Models\PosOrderItem;
use App\Modules\Simaksi\Models\SimaksiBooking;
use App\Modules\Store\Models\Product;
use App\Shared\Enums\BookingStatus;
use App\Shared\Enums\PaymentMethod;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AnalyticsDataSeeder extends Seeder
{
    public function run(): void
    {
        $currentYear = 2026;
        $kasir = User::where('role', 'KASIR')->first() ?? User::first();
        $mitra1 = ConsignmentMitra::first();

        // 1. Seed Monthly Bookings & Hikers (Jan s/d Okt 2026)
        $monthHikerTargets = [
            1 => 180, // Jan (Hujan)
            2 => 140, // Feb (Hujan)
            3 => 220, // Mar (Peralihan)
            4 => 340, // Apr (Pasca Lebaran)
            5 => 510, // Mei (Awal Kemarau)
            6 => 720, // Jun (Liburan Sekolah)
            7 => 940, // Jul (Peak Season Gunung Cikuray)
            8 => 880, // Agu (Kemerdekaan 17 Agustus)
            9 => 560, // Sep (Musim Cantigi)
            10 => 480, // Okt (Akhir Kemarau)
        ];

        $sampleLeaders = [
            'Rian Hidayat', 'Fajar Pratama', 'Bagus Wicaksono', 'Dian Nugraha',
            'Irfan Hakim', 'Ahmad Fauzi', 'Doni Saputra', 'Aris Munandar',
            'Siti Rahmawati', 'Dewi Lestari', 'Yoga Pratama', 'Eko Prasetyo',
            'Gilang Ramadhan', 'Hendra Gunawan', 'Ilham Maulana', 'Joko Susilo',
            'Budi Setiawan', 'Wahyu Hidayat', 'Andi Kurniawan', 'Rizky Saputra'
        ];

        $sampleCities = ['Bandung', 'Jakarta', 'Bogor', 'Bekasi', 'Tangerang', 'Garut', 'Tasikmalaya', 'Depok'];

        foreach ($monthHikerTargets as $month => $targetHikers) {
            $accumulatedHikers = 0;
            $day = 1;
            $daysInMonth = Carbon::create($currentYear, $month, 1)->daysInMonth;

            while ($accumulatedHikers < $targetHikers && $day <= $daysInMonth) {
                $date = Carbon::create($currentYear, $month, $day);
                $isWeekend = $date->isWeekend();
                $isSaturday = $date->isSaturday();

                // Weekend memiliki volume pendaki lebih besar
                $groupSize = $isSaturday ? rand(4, 9) : ($isWeekend ? rand(3, 6) : rand(2, 4));
                $accumulatedHikers += $groupSize;

                $leaderName = $sampleLeaders[array_rand($sampleLeaders)];
                $city = $sampleCities[array_rand($sampleCities)];
                $ticketAmount = $groupSize * 25000;
                $bookingCode = sprintf('SMX-%02d%02d-%03d', $month, $day, rand(100, 999));

                $booking = SimaksiBooking::updateOrCreate(
                    ['booking_code' => $bookingCode],
                    [
                        'hiking_date' => $date->toDateString(),
                        'duration_days' => 2,
                        'leader_name' => $leaderName,
                        'leader_nik' => '32' . rand(10000000000000, 99999999999999),
                        'leader_phone' => '08' . rand(1111111111, 9999999999),
                        'leader_email' => strtolower(str_replace(' ', '.', $leaderName)) . '@example.com',
                        'leader_address' => 'Jl. Merdeka No. ' . rand(1, 99) . ', ' . $city,
                        'emergency_contact_name' => 'Keluarga ' . $leaderName,
                        'emergency_contact_phone' => '08' . rand(1111111111, 9999999999),
                        'total_hikers' => $groupSize,
                        'ticket_fee_per_person' => 25000,
                        'total_ticket_amount' => $ticketAmount,
                        'eco_deposit_amount' => 20000,
                        'status' => BookingStatus::CHECKED_OUT,
                        'payment_method' => rand(0, 1) ? PaymentMethod::QRIS : PaymentMethod::TRANSFER,
                        'paid_at' => $date->copy()->subDays(2),
                        'checkin_at' => $date->copy()->setHour(7),
                        'checkout_at' => $date->copy()->addDay()->setHour(14),
                        'current_pos' => 'Basecamp',
                        'qr_token' => 'QR-' . Str::upper(Str::random(12)),
                    ]
                );

                $day += $isWeekend ? 1 : rand(1, 3);
            }
        }

        // 2. Seed Website Visits untuk Conversion Rate Funnel
        // Rasio realistis: 2.500 total kunjungan unik menghasilkan ~250 booking (~10% conversion)
        $totalTargetVisits = 2650;
        $currentVisits = WebsiteVisit::count();

        if ($currentVisits < $totalTargetVisits) {
            $neededVisits = $totalTargetVisits - $currentVisits;
            $visitTypes = ['landing', 'landing', 'booking', 'trail_map', 'rental', 'store'];

            for ($i = 0; $i < $neededVisits; $i++) {
                $month = rand(1, 10);
                $day = rand(1, Carbon::create($currentYear, $month, 1)->daysInMonth);
                $visitDate = Carbon::create($currentYear, $month, $day)->toDateString();
                $type = $visitTypes[array_rand($visitTypes)];

                WebsiteVisit::create([
                    'session_id' => 'sess_' . md5($i . '_' . $visitDate . '_' . rand(1000, 9999)),
                    'ip_hash' => hash('sha256', 'ip_' . rand(1, 1000)),
                    'page_path' => $type === 'landing' ? '/' : '/' . $type,
                    'page_type' => $type,
                    'visited_date' => $visitDate,
                    'created_at' => Carbon::create($currentYear, $month, $day, rand(6, 22), rand(0, 59)),
                ]);
            }
        }

        // 3. Seed Top Produk Terjual di Toko (POS Order Items)
        $sampleProductsSales = [
            ['name' => 'Gas Butane Hi-Cook 230g', 'type' => 'STORE_BELI_PUTUS', 'price' => 24000, 'cost' => 17000, 'qty' => 165],
            ['name' => 'Kopi Robusta Cikuray Roast 200g (Mang Asep)', 'type' => 'STORE_KONSINYASI', 'price' => 38000, 'cost' => 28000, 'qty' => 112],
            ['name' => 'Jas Hujan Ponco Kelelawar', 'type' => 'STORE_BELI_PUTUS', 'price' => 18000, 'cost' => 11000, 'qty' => 84],
            ['name' => 'Madu Hutan Murni Cikuray 250ml (Kang Jajang)', 'type' => 'STORE_KONSINYASI', 'price' => 65000, 'cost' => 50000, 'qty' => 67],
            ['name' => 'Keripik Singkong Balado Bi Enok', 'type' => 'STORE_KONSINYASI', 'price' => 15000, 'cost' => 11000, 'qty' => 76],
            ['name' => 'Baterai Alkaline AAA 2-Pack', 'type' => 'STORE_BELI_PUTUS', 'price' => 16000, 'cost' => 10000, 'qty' => 45],
            ['name' => 'Sarung Tangan Grip Karet Gunung', 'type' => 'STORE_BELI_PUTUS', 'price' => 12000, 'cost' => 7000, 'qty' => 53],
            ['name' => 'Mie Instan Goreng / Rebus + Telur', 'type' => 'STORE_BELI_PUTUS', 'price' => 12000, 'cost' => 6000, 'qty' => 98],
        ];

        // Buat invoice agregat POS untuk menampung data penjualan produk
        $posBatch = PosOrder::firstOrCreate(
            ['invoice_number' => 'INV-HISTORICAL-STORE-2026'],
            [
                'customer_name' => 'Penjualan Agregat Kasir Basecamp 2026',
                'customer_phone' => '081200000000',
                'cashier_user_id' => $kasir->id,
                'subtotal_simaksi' => 0,
                'subtotal_store' => 12450000,
                'subtotal_rental' => 0,
                'total_amount' => 12450000,
                'paid_amount' => 12450000,
                'payment_method' => PaymentMethod::CASH,
                'payment_status' => 'PAID',
            ]
        );

        $dbProducts = Product::all()->keyBy('name');

        foreach ($sampleProductsSales as $sale) {
            $matchedProduct = $dbProducts->first(function ($p) use ($sale) {
                return str_contains(strtolower($p->name), strtolower(explode(' ', $sale['name'])[0]));
            });

            PosOrderItem::updateOrCreate(
                [
                    'pos_order_id' => $posBatch->id,
                    'item_name' => $sale['name'],
                ],
                [
                    'item_type' => $sale['type'],
                    'reference_id' => $matchedProduct ? $matchedProduct->id : null,
                    'qty' => $sale['qty'],
                    'unit_price' => $sale['price'],
                    'subtotal' => $sale['qty'] * $sale['price'],
                    'cost_price' => $sale['cost'],
                    'mitra_id' => $sale['type'] === 'STORE_KONSINYASI' ? ($mitra1 ? $mitra1->id : null) : null,
                ]
            );
        }
    }
}
