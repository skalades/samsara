<?php

namespace Database\Seeders;

use App\Modules\Ojek\Models\OjekDriver;
use App\Modules\Ojek\Models\OjekDriverAttendance;
use App\Modules\Ojek\Models\OjekQueueTicket;
use App\Modules\Ojek\Models\OjekRoute;
use App\Shared\Enums\OjekDriverStatus;
use App\Shared\Enums\OjekQueueStatus;
use Illuminate\Database\Seeder;

class OjekSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Rute Resmi Ojek Gunung Cikuray via Cigedug
        $routesData = [
            [
                'route_name' => 'Pertigaan Pasar Cigedug ⇄ Basecamp SAMSARA',
                'origin' => 'Pertigaan Pasar Cigedug',
                'destination' => 'Basecamp SAMSARA',
                'fare_amount' => 20000,
                'driver_share' => 17000,
                'kas_share' => 3000,
                'estimated_duration_minutes' => 15,
                'is_active' => true,
            ],
            [
                'route_name' => 'Basecamp SAMSARA ⇄ Pintu Rimba (Pos 1 Kebun Teh)',
                'origin' => 'Basecamp SAMSARA',
                'destination' => 'Pintu Rimba / Pos 1 Kebun Teh',
                'fare_amount' => 25000,
                'driver_share' => 22000,
                'kas_share' => 3000,
                'estimated_duration_minutes' => 20,
                'is_active' => true,
            ],
            [
                'route_name' => 'Terminal Guntur Garut ⇄ Basecamp SAMSARA',
                'origin' => 'Terminal Guntur Garut',
                'destination' => 'Basecamp SAMSARA',
                'fare_amount' => 60000,
                'driver_share' => 52000,
                'kas_share' => 8000,
                'estimated_duration_minutes' => 45,
                'is_active' => true,
            ],
            [
                'route_name' => 'Stasiun Kereta Garut ⇄ Basecamp SAMSARA',
                'origin' => 'Stasiun Kereta Garut Kota',
                'destination' => 'Basecamp SAMSARA',
                'fare_amount' => 65000,
                'driver_share' => 56000,
                'kas_share' => 9000,
                'estimated_duration_minutes' => 50,
                'is_active' => true,
            ],
        ];

        $routes = [];
        foreach ($routesData as $r) {
            $routes[] = OjekRoute::updateOrCreate(['route_name' => $r['route_name']], $r);
        }

        // 2. Anggota Driver Paguyuban Ojek Cigedug Mandiri
        $driversData = [
            [
                'driver_number' => 'DRV-001',
                'name' => 'Kang Asep Kurnia',
                'phone' => '081223344001',
                'license_plate' => 'Z 4821 SG',
                'motorcycle_model' => 'Honda CRF 150L (Trail)',
                'community_name' => 'Paguyuban Ojek Cigedug Mandiri',
                'is_active' => true,
                'total_trips' => 42,
            ],
            [
                'driver_number' => 'DRV-002',
                'name' => 'Kang Dadang Suherman',
                'phone' => '081223344002',
                'license_plate' => 'Z 3392 DA',
                'motorcycle_model' => 'Yamaha WR 155R',
                'community_name' => 'Paguyuban Ojek Cigedug Mandiri',
                'is_active' => true,
                'total_trips' => 38,
            ],
            [
                'driver_number' => 'DRV-003',
                'name' => 'Kang Yayan Mulyana',
                'phone' => '081223344003',
                'license_plate' => 'Z 5119 ER',
                'motorcycle_model' => 'Honda Megapro Modif Trail',
                'community_name' => 'Paguyuban Ojek Cigedug Mandiri',
                'is_active' => true,
                'total_trips' => 29,
            ],
            [
                'driver_number' => 'DRV-004',
                'name' => 'Kang Cecep Hendrawan',
                'phone' => '081223344004',
                'license_plate' => 'Z 2940 FX',
                'motorcycle_model' => 'Kawasaki KLX 150',
                'community_name' => 'Paguyuban Ojek Cigedug Mandiri',
                'is_active' => true,
                'total_trips' => 51,
            ],
            [
                'driver_number' => 'DRV-005',
                'name' => 'Kang Ujang Supriatna',
                'phone' => '081223344005',
                'license_plate' => 'Z 6182 KL',
                'motorcycle_model' => 'Honda Beat Street',
                'community_name' => 'Paguyuban Ojek Cigedug Mandiri',
                'is_active' => true,
                'total_trips' => 19,
            ],
            [
                'driver_number' => 'DRV-006',
                'name' => 'Kang Rahmat Hidayat',
                'phone' => '081223344006',
                'license_plate' => 'Z 4028 WA',
                'motorcycle_model' => 'Yamaha Jupiter MX King',
                'community_name' => 'Paguyuban Ojek Cigedug Mandiri',
                'is_active' => true,
                'total_trips' => 22,
            ],
        ];

        $today = today()->toDateString();
        $order = 1;

        foreach ($driversData as $d) {
            $driver = OjekDriver::updateOrCreate(['driver_number' => $d['driver_number']], $d);

            // Check-in kan ke antrean giliran hari ini
            OjekDriverAttendance::updateOrCreate(
                [
                    'ojek_driver_id' => $driver->id,
                    'date' => $today,
                ],
                [
                    'queue_order' => $order++,
                    'status' => OjekDriverStatus::STANDBY,
                    'checked_in_at' => now()->subMinutes(60 - ($order * 5)),
                    'daily_trips_count' => rand(1, 3),
                ]
            );
        }

        // 3. Dummy Tiket Awal untuk Demonstrasi Antrean
        $datePrefix = now()->format('ymd');

        // Tiket 1: Selesai
        OjekQueueTicket::updateOrCreate(
            ['ticket_number' => "OJ-{$datePrefix}-001"],
            [
                'ojek_route_id' => $routes[0]->id,
                'passenger_name' => 'Rian Pratama (Jakarta)',
                'passenger_phone' => '081399887766',
                'source' => 'LOKET_POS',
                'assigned_driver_id' => 1,
                'fare_amount' => $routes[0]->fare_amount,
                'payment_status' => 'PAID',
                'payment_method' => 'QRIS',
                'status' => OjekQueueStatus::COMPLETED,
                'called_at' => now()->subMinutes(40),
                'started_at' => now()->subMinutes(35),
                'completed_at' => now()->subMinutes(15),
            ]
        );

        // Tiket 2: Sedang Dipanggil
        $firstAttendance = OjekDriverAttendance::whereDate('date', $today)->where('queue_order', 1)->first();
        if ($firstAttendance) {
            $firstAttendance->update([
                'status' => OjekDriverStatus::CALLED,
                'last_called_at' => now()->subMinutes(2),
            ]);

            OjekQueueTicket::updateOrCreate(
                ['ticket_number' => "OJ-{$datePrefix}-002"],
                [
                    'ojek_route_id' => $routes[1]->id,
                    'passenger_name' => 'Budi Santoso (Bandung)',
                    'passenger_phone' => '085711223344',
                    'source' => 'LOKET_POS',
                    'assigned_driver_id' => $firstAttendance->ojek_driver_id,
                    'assigned_attendance_id' => $firstAttendance->id,
                    'fare_amount' => $routes[1]->fare_amount,
                    'payment_status' => 'PAID',
                    'payment_method' => 'CASH',
                    'status' => OjekQueueStatus::CALLED,
                    'called_at' => now()->subMinutes(2),
                ]
            );
        }

        // Tiket 3: Sedang Menunggu
        OjekQueueTicket::updateOrCreate(
            ['ticket_number' => "OJ-{$datePrefix}-003"],
            [
                'ojek_route_id' => $routes[0]->id,
                'passenger_name' => 'Fajar Nugraha (Tangerang)',
                'passenger_phone' => '081298765432',
                'source' => 'PANGKALAN_WALKIN',
                'fare_amount' => $routes[0]->fare_amount,
                'payment_status' => 'PAID',
                'payment_method' => 'CASH',
                'status' => OjekQueueStatus::WAITING,
            ]
        );
    }
}
