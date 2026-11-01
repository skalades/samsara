<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Consignment\Models\ConsignmentMitra;
use App\Modules\FieldOps\Models\EcoDeposit;
use App\Modules\POS\Models\CashierShift;
use App\Modules\POS\Models\PosOrder;
use App\Modules\POS\Models\PosOrderItem;
use App\Modules\Rental\Models\RentalItem;
use App\Modules\Simaksi\Models\DailyQuota;
use App\Modules\Simaksi\Models\SimaksiBooking;
use App\Modules\Simaksi\Models\SimaksiMember;
use App\Modules\Simaksi\Models\TrailCheckpoint;
use App\Modules\Simaksi\Models\TrailSetting;
use App\Modules\Store\Models\Product;
use App\Shared\Enums\BookingStatus;
use App\Shared\Enums\InventoryType;
use App\Shared\Enums\PaymentMethod;
use App\Shared\Enums\ProductCategory;
use App\Shared\Enums\RentalAssetStatus;
use App\Shared\Enums\TrailStatus;
use App\Shared\Enums\UserRole;
use App\Shared\Enums\WeatherStatus;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Initial Trail Setting
        TrailSetting::updateOrCreate(
            ['id' => 1],
            [
                'trail_status' => TrailStatus::OPEN,
                'weather_status' => WeatherStatus::CERAH,
                'announcement' => 'Jalur pendakian Cikuray via Cigedug BUKA NORMAL. Angin sejuk di Pos 4 Samudra Awan. Seluruh pendaki wajib membawa trashbag!',
            ]
        );

        // 2. Staff & Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@samsara.id'],
            [
                'name' => 'Koordinator Utama SAMSARA',
                'password' => Hash::make('password'),
                'role' => UserRole::SUPERADMIN,
                'phone' => '081223456789',
                'assigned_pos' => 'Basecamp',
            ]
        );

        $sar = User::firstOrCreate(
            ['email' => 'sar@samsara.id'],
            [
                'name' => 'Dadan Komandan SAR SAMSARA',
                'password' => Hash::make('password'),
                'role' => UserRole::ADMIN_SAR,
                'phone' => '081298765432',
                'assigned_pos' => 'Pos 4 (Samudra Awan)',
            ]
        );

        $kasir = User::firstOrCreate(
            ['email' => 'kasir@samsara.id'],
            [
                'name' => 'Rina Loket Kasir SAMSARA',
                'password' => Hash::make('password'),
                'role' => UserRole::KASIR,
                'phone' => '085711223344',
                'assigned_pos' => 'Basecamp',
            ]
        );

        $pos1 = User::firstOrCreate(
            ['email' => 'pos1@samsara.id'],
            [
                'name' => 'Kang Ujang Petugas Pos 1',
                'password' => Hash::make('password'),
                'role' => UserRole::PETUGAS_POS,
                'phone' => '085811223344',
                'assigned_pos' => 'Pos 1 (Tanjakan EOD)',
            ]
        );

        $pos4 = User::firstOrCreate(
            ['email' => 'pos4@samsara.id'],
            [
                'name' => 'Kang Cecep Petugas Pos 4',
                'password' => Hash::make('password'),
                'role' => UserRole::PETUGAS_POS,
                'phone' => '085911223344',
                'assigned_pos' => 'Pos 4 (Samudra Awan)',
            ]
        );

        // 3. Mitra Konsinyasi UMKM Warga Cigedug
        $mitra1 = ConsignmentMitra::firstOrCreate(
            ['name' => 'Mang Asep (Kopi & Gula Aren Cigedug)'],
            [
                'village_address' => 'Kampung Pasir Eurih, RT 02/04 Cigedug',
                'phone_wa' => '081320112233',
                'bank_info' => 'BRI 4123-0100-9876-531 a/n Asep Mulyana',
                'active' => true,
            ]
        );

        $mitra2 = ConsignmentMitra::firstOrCreate(
            ['name' => 'Bi Enok (Keripik Singkong & Ranginang)'],
            [
                'village_address' => 'Kampung Cigedug Girang, RT 01/02 Cigedug',
                'phone_wa' => '082133445566',
                'bank_info' => 'BCA 1480-9988-77 a/n Enok Suhaeti',
                'active' => true,
            ]
        );

        $mitra3 = ConsignmentMitra::firstOrCreate(
            ['name' => 'Kang Jajang (Madu Hutan Murni Cikuray)'],
            [
                'village_address' => 'Kampung Babakan Cikuray, RT 03/05 Cigedug',
                'phone_wa' => '087755667788',
                'bank_info' => 'Mandiri 131-00-1928374 a/n Jajang Rohimat',
                'active' => true,
            ]
        );

        // 4. Products (Dual Inventory)
        $products = [
            // Beli Putus SAMSARA
            [
                'sku' => 'GAS-001',
                'name' => 'Gas Butane Hi-Cook 230g',
                'category' => ProductCategory::GAS_BAHAN_BAKAR,
                'inventory_type' => InventoryType::BELI_PUTUS,
                'consignment_mitra_id' => null,
                'cost_price' => 17000,
                'selling_price' => 24000,
                'stock' => 45,
                'minimum_stock_alert' => 10,
                'description' => 'Gas portable kaleng standar untuk kompor pendakian gunung.',
            ],
            [
                'sku' => 'JAS-001',
                'name' => 'Jas Hujan Ponco Kelelawar',
                'category' => ProductCategory::PERLENGKAPAN,
                'inventory_type' => InventoryType::BELI_PUTUS,
                'consignment_mitra_id' => null,
                'cost_price' => 12000,
                'selling_price' => 20000,
                'stock' => 30,
                'minimum_stock_alert' => 5,
                'description' => 'Jas hujan tebal tahan angin untuk perlindungan di vegetasi basah Cigedug.',
            ],
            [
                'sku' => 'EMB-001',
                'name' => 'Emergency Blanket Aluminium Foil',
                'category' => ProductCategory::PERLENGKAPAN,
                'inventory_type' => InventoryType::BELI_PUTUS,
                'consignment_mitra_id' => null,
                'cost_price' => 8000,
                'selling_price' => 15000,
                'stock' => 25,
                'minimum_stock_alert' => 8,
                'description' => 'Selimut darurat penahan panas tubuh anti hipotermia.',
            ],
            [
                'sku' => 'LOG-001',
                'name' => 'Paket Logistik Darurat (Mi, Biskuit, Cokelat)',
                'category' => ProductCategory::LOGISTIK,
                'inventory_type' => InventoryType::BELI_PUTUS,
                'consignment_mitra_id' => null,
                'cost_price' => 15000,
                'selling_price' => 25000,
                'stock' => 20,
                'minimum_stock_alert' => 5,
                'description' => 'Kalori instan siap seduh untuk perbekalan mendadak.',
            ],
            [
                'sku' => 'AIR-001',
                'name' => 'Pocari Sweat 500ml',
                'category' => ProductCategory::MAKANAN_MINUMAN,
                'inventory_type' => InventoryType::BELI_PUTUS,
                'consignment_mitra_id' => null,
                'cost_price' => 7000,
                'selling_price' => 10000,
                'stock' => 40,
                'minimum_stock_alert' => 12,
                'description' => 'Minuman isotonik pengganti ion tubuh di tanjakan terjal EOD.',
            ],
            // Titip Jual Konsinyasi UMKM Cigedug
            [
                'sku' => 'KOP-001',
                'name' => 'Kopi Robusta Cikuray Roast 200g (Mang Asep)',
                'category' => ProductCategory::PRODUK_LOKAL,
                'inventory_type' => InventoryType::KONSINYASI,
                'consignment_mitra_id' => $mitra1->id,
                'cost_price' => 28000, // Harga setoran Mang Asep
                'selling_price' => 38000, // Harga jual basecamp
                'stock' => 18,
                'minimum_stock_alert' => 4,
                'description' => 'Biji kopi asli lereng Cikuray Cigedug dengan aroma rempah dan cokelat.',
            ],
            [
                'sku' => 'GUL-001',
                'name' => 'Gula Semut Aren Asli Cigedug 250g',
                'category' => ProductCategory::PRODUK_LOKAL,
                'inventory_type' => InventoryType::KONSINYASI,
                'consignment_mitra_id' => $mitra1->id,
                'cost_price' => 18000,
                'selling_price' => 25000,
                'stock' => 15,
                'minimum_stock_alert' => 4,
                'description' => 'Pemanis alami dari nira aren pegunungan Garut Selatan.',
            ],
            [
                'sku' => 'KRP-001',
                'name' => 'Keripik Singkong Balado Renyah (Bi Enok)',
                'category' => ProductCategory::PRODUK_LOKAL,
                'inventory_type' => InventoryType::KONSINYASI,
                'consignment_mitra_id' => $mitra2->id,
                'cost_price' => 10000,
                'selling_price' => 15000,
                'stock' => 22,
                'minimum_stock_alert' => 6,
                'description' => 'Camilan khas Cigedug lezat dan gurih tanpa bahan pengawet.',
            ],
            [
                'sku' => 'MAD-001',
                'name' => 'Madu Hutan Odeng Liar Cikuray 150ml (Kang Jajang)',
                'category' => ProductCategory::PRODUK_LOKAL,
                'inventory_type' => InventoryType::KONSINYASI,
                'consignment_mitra_id' => $mitra3->id,
                'cost_price' => 45000,
                'selling_price' => 60000,
                'stock' => 10,
                'minimum_stock_alert' => 3,
                'description' => 'Madu lebah liar dari pohon ramin puncak Cikuray penambah stamina fisik.',
            ],
        ];

        foreach ($products as $p) {
            Product::updateOrCreate(['sku' => $p['sku']], $p);
        }

        // 5. Rental Items
        $rentals = [
            ['asset_code' => 'TND-01', 'name' => 'Tenda Dome 4P Great Outdoor Java', 'category' => 'TENDA', 'rental_rate_per_day' => 45000, 'deposit_rate' => 50000, 'status' => RentalAssetStatus::TERSEDIA],
            ['asset_code' => 'TND-02', 'name' => 'Tenda Dome 4P Great Outdoor Java', 'category' => 'TENDA', 'rental_rate_per_day' => 45000, 'deposit_rate' => 50000, 'status' => RentalAssetStatus::DISEWA],
            ['asset_code' => 'TND-03', 'name' => 'Tenda Dome 2P Eiger Storm', 'category' => 'TENDA', 'rental_rate_per_day' => 35000, 'deposit_rate' => 40000, 'status' => RentalAssetStatus::PERLU_QC_CUCI],
            ['asset_code' => 'TND-04', 'name' => 'Tenda Dome 4P Consina Magnum', 'category' => 'TENDA', 'rental_rate_per_day' => 50000, 'deposit_rate' => 50000, 'status' => RentalAssetStatus::DALAM_PERBAIKAN],
            ['asset_code' => 'KMP-01', 'name' => 'Kompor Kotak Windproof Kovar', 'category' => 'KOMPOR', 'rental_rate_per_day' => 15000, 'deposit_rate' => 20000, 'status' => RentalAssetStatus::TERSEDIA],
            ['asset_code' => 'KMP-02', 'name' => 'Kompor Ultralight Spider BRS', 'category' => 'KOMPOR', 'rental_rate_per_day' => 20000, 'deposit_rate' => 20000, 'status' => RentalAssetStatus::TERSEDIA],
            ['asset_code' => 'NST-01', 'name' => 'Nesting Cooking Set DS-308', 'category' => 'NESTING', 'rental_rate_per_day' => 20000, 'deposit_rate' => 25000, 'status' => RentalAssetStatus::TERSEDIA],
            ['asset_code' => 'SB-01', 'name' => 'Sleeping Bag Polar Bulu Tebal', 'category' => 'SLEEPING_BAG', 'rental_rate_per_day' => 15000, 'deposit_rate' => 20000, 'status' => RentalAssetStatus::TERSEDIA],
            ['asset_code' => 'SB-02', 'name' => 'Sleeping Bag Polar Bulu Tebal', 'category' => 'SLEEPING_BAG', 'rental_rate_per_day' => 15000, 'deposit_rate' => 20000, 'status' => RentalAssetStatus::PERLU_QC_CUCI],
            ['asset_code' => 'HL-01', 'name' => 'Headlamp LED Rechargeable 500Lm', 'category' => 'HEADLAMP', 'rental_rate_per_day' => 10000, 'deposit_rate' => 15000, 'status' => RentalAssetStatus::TERSEDIA],
            ['asset_code' => 'TRK-01', 'name' => 'Trekking Pole Duralumin 3-Section', 'category' => 'TRACKING_POLE', 'rental_rate_per_day' => 10000, 'deposit_rate' => 15000, 'status' => RentalAssetStatus::TERSEDIA],
        ];

        foreach ($rentals as $r) {
            RentalItem::updateOrCreate(['asset_code' => $r['asset_code']], $r);
        }

        // 6. Quotas for next 30 days
        $start = today();
        for ($i = 0; $i < 30; $i++) {
            $date = $start->copy()->addDays($i);
            $isWeekend = $date->isWeekend();
            DailyQuota::updateOrCreate(
                ['date' => $date->toDateString()],
                [
                    'max_quota' => $isWeekend ? 300 : 150,
                    'booked_count' => ($i === 0 ? 142 : ($i === 1 ? 88 : rand(5, 45))),
                    'is_blackout' => ($i === 14), // Example blackout 2 weeks from now
                    'notes' => ($i === 14 ? 'Jalur Ditutup: Pembersihan & Pemulihan Ekosistem' : null),
                ]
            );
        }

        // 7. Seed Sample Bookings & Manifest on Trail
        $sampleBooking1 = SimaksiBooking::updateOrCreate(
            ['qr_token' => 'QR-DEMO-SMX-A101'],
            [
                'booking_code' => 'SMX-' . today()->format('ymd') . '-A101',
                'hiking_date' => today()->toDateString(),
                'duration_days' => 2,
                'leader_name' => 'Bayu Pratama',
                'leader_nik' => '3205121995080001',
                'leader_phone' => '081234567890',
                'leader_email' => 'bayu.pratama@gmail.com',
                'leader_address' => 'Jl. Dago Asri No. 42, Coblong, Bandung',
                'emergency_contact_name' => 'Ibu Hendrawan (Ibu Kandung)',
                'emergency_contact_phone' => '081398765432',
                'total_hikers' => 4,
                'ticket_fee_per_person' => 25000,
                'total_ticket_amount' => 100000,
                'eco_deposit_amount' => 20000,
                'status' => BookingStatus::ON_TRAIL,
                'payment_method' => PaymentMethod::QRIS,
                'paid_at' => today()->subHours(4),
                'checkin_at' => today()->subHours(3),
                'current_pos' => 'Pos 4 (Samudra Awan)',
                'tent_color_details' => 'Tenda Dome Orange 4P, Cover Bag Hitam & Kuning',
                'notes' => 'Rombongan Mahasiswa Mapala Unpad',
            ]
        );

        // Members for Booking 1
        SimaksiMember::updateOrCreate(
            ['booking_id' => $sampleBooking1->id, 'nik' => '3205121995080001'],
            ['full_name' => 'Bayu Pratama (Ketua)', 'phone' => '081234567890', 'gender' => 'L', 'age' => 23, 'medical_history' => 'Tidak ada riwayat penyakit']
        );
        SimaksiMember::updateOrCreate(
            ['booking_id' => $sampleBooking1->id, 'nik' => '3205121996020002'],
            ['full_name' => 'Fikri Haikal', 'phone' => '081234567891', 'gender' => 'L', 'age' => 22, 'medical_history' => 'Asma ringan (membawa inhaler pribadi)']
        );
        SimaksiMember::updateOrCreate(
            ['booking_id' => $sampleBooking1->id, 'nik' => '3205121997050003'],
            ['full_name' => 'Sarah Amalia', 'phone' => '081234567892', 'gender' => 'P', 'age' => 21, 'medical_history' => 'Alergi dingin']
        );
        SimaksiMember::updateOrCreate(
            ['booking_id' => $sampleBooking1->id, 'nik' => '3205121996110004'],
            ['full_name' => 'Rizky Ramadhan', 'phone' => '081234567893', 'gender' => 'L', 'age' => 23, 'medical_history' => 'Tidak ada riwayat penyakit']
        );

        // Eco-deposit for Booking 1
        EcoDeposit::updateOrCreate(
            ['booking_id' => $sampleBooking1->id],
            [
                'deposit_amount' => 20000,
                'brought_plastics_count' => 8,
                'brought_cans_count' => 3,
                'brought_bottles_count' => 6,
                'deposit_status' => 'HELD',
                'checked_in_by' => $pos1->id,
            ]
        );

        // Checkpoints for Booking 1
        TrailCheckpoint::firstOrCreate(
            ['booking_id' => $sampleBooking1->id, 'pos_name' => 'Pos 1 (Tanjakan EOD)'],
            ['scanned_by_user_id' => $pos1->id, 'direction' => 'ASCENDING', 'hikers_passed_count' => 4, 'notes' => 'Rombongan lengkap & sehat']
        );
        TrailCheckpoint::firstOrCreate(
            ['booking_id' => $sampleBooking1->id, 'pos_name' => 'Pos 2 (Batu Gendrung)'],
            ['scanned_by_user_id' => $pos1->id, 'direction' => 'ASCENDING', 'hikers_passed_count' => 4, 'notes' => 'Melintas santai']
        );
        TrailCheckpoint::firstOrCreate(
            ['booking_id' => $sampleBooking1->id, 'pos_name' => 'Pos 3 (Alam Rimba)'],
            ['scanned_by_user_id' => $pos4->id, 'direction' => 'ASCENDING', 'hikers_passed_count' => 4, 'notes' => 'Istirahat makan siang']
        );
        TrailCheckpoint::firstOrCreate(
            ['booking_id' => $sampleBooking1->id, 'pos_name' => 'Pos 4 (Samudra Awan)'],
            ['scanned_by_user_id' => $pos4->id, 'direction' => 'ASCENDING', 'hikers_passed_count' => 4, 'notes' => 'Camp di area shelter 2']
        );

        // Sample Booking 2 (at Pos 2)
        $sampleBooking2 = SimaksiBooking::updateOrCreate(
            ['qr_token' => 'QR-DEMO-SMX-B202'],
            [
                'booking_code' => 'SMX-' . today()->format('ymd') . '-B202',
                'hiking_date' => today()->toDateString(),
                'duration_days' => 2,
                'leader_name' => 'Dimas Arya',
                'leader_nik' => '3273111998010005',
                'leader_phone' => '082155667788',
                'leader_email' => 'dimas.arya@outlook.com',
                'leader_address' => 'Jl. Pajajaran No. 15, Bogor',
                'emergency_contact_name' => 'Bapak Surya (Ayah)',
                'emergency_contact_phone' => '081288776655',
                'total_hikers' => 3,
                'ticket_fee_per_person' => 25000,
                'total_ticket_amount' => 75000,
                'eco_deposit_amount' => 20000,
                'status' => BookingStatus::ON_TRAIL,
                'payment_method' => PaymentMethod::CASH,
                'paid_at' => today()->subHours(2),
                'checkin_at' => today()->subHours(2),
                'current_pos' => 'Pos 2 (Batu Gendrung)',
                'tent_color_details' => 'Tenda Biru 3P Eiger',
                'notes' => 'Jalur Cigedug pertama kali',
            ]
        );

        SimaksiMember::updateOrCreate(
            ['booking_id' => $sampleBooking2->id, 'nik' => '3273111998010005'],
            ['full_name' => 'Dimas Arya', 'phone' => '082155667788', 'gender' => 'L', 'age' => 26, 'medical_history' => 'Sehat prima']
        );
        SimaksiMember::updateOrCreate(
            ['booking_id' => $sampleBooking2->id, 'nik' => '3273111998010006'],
            ['full_name' => 'Aditya Pratama', 'phone' => '082155667789', 'gender' => 'L', 'age' => 25, 'medical_history' => 'Pernah dislokasi lutut']
        );
        SimaksiMember::updateOrCreate(
            ['booking_id' => $sampleBooking2->id, 'nik' => '3273111998010007'],
            ['full_name' => 'Nabila Putri', 'phone' => '082155667790', 'gender' => 'P', 'age' => 24, 'medical_history' => 'Sehat prima']
        );

        EcoDeposit::updateOrCreate(
            ['booking_id' => $sampleBooking2->id],
            [
                'deposit_amount' => 20000,
                'brought_plastics_count' => 6,
                'brought_cans_count' => 2,
                'brought_bottles_count' => 4,
                'deposit_status' => 'HELD',
                'checked_in_by' => $pos1->id,
            ]
        );

        TrailCheckpoint::firstOrCreate(
            ['booking_id' => $sampleBooking2->id, 'pos_name' => 'Pos 1 (Tanjakan EOD)'],
            ['scanned_by_user_id' => $pos1->id, 'direction' => 'ASCENDING', 'hikers_passed_count' => 3, 'notes' => 'Kondisi baik']
        );
        TrailCheckpoint::firstOrCreate(
            ['booking_id' => $sampleBooking2->id, 'pos_name' => 'Pos 2 (Batu Gendrung)'],
            ['scanned_by_user_id' => $pos1->id, 'direction' => 'ASCENDING', 'hikers_passed_count' => 3, 'notes' => 'Melintas ke Pos 3']
        );

        // 8. Cashier Shift & Sample POS Invoices
        $shift = CashierShift::firstOrCreate(
            ['cashier_user_id' => $kasir->id, 'status' => 'OPEN'],
            [
                'shift_number' => 1,
                'opened_at' => today()->setHour(7)->setMinute(0),
                'opening_cash' => 500000,
            ]
        );

        $posOrder1 = PosOrder::firstOrCreate(
            ['invoice_number' => 'INV-' . today()->format('ymd') . '-001'],
            [
                'booking_id' => $sampleBooking1->id,
                'cashier_user_id' => $kasir->id,
                'customer_name' => 'Bayu Pratama (Mapala Unpad)',
                'customer_phone' => '081234567890',
                'subtotal_simaksi' => 100000,
                'subtotal_store' => 86000,
                'subtotal_rental' => 45000,
                'eco_deposit' => 20000,
                'rental_deposit' => 50000,
                'total_amount' => 301000,
                'paid_amount' => 301000,
                'change_amount' => 0,
                'payment_method' => PaymentMethod::QRIS,
                'payment_status' => 'PAID',
                'is_preorder_pickup' => true,
                'notes' => 'Pembayaran Tiket + Gas + Kopi Mang Asep + Sewa Tenda',
            ]
        );

        // Items for PosOrder 1
        PosOrderItem::firstOrCreate(
            ['pos_order_id' => $posOrder1->id, 'item_name' => 'Tiket SIMAKSI Cikuray (4 orang)'],
            ['item_type' => 'SIMAKSI', 'reference_id' => $sampleBooking1->id, 'qty' => 4, 'unit_price' => 25000, 'subtotal' => 100000, 'cost_price' => 0]
        );
        PosOrderItem::firstOrCreate(
            ['pos_order_id' => $posOrder1->id, 'item_name' => 'Gas Butane Hi-Cook 230g'],
            ['item_type' => 'STORE_BELI_PUTUS', 'reference_id' => 1, 'qty' => 2, 'unit_price' => 24000, 'subtotal' => 48000, 'cost_price' => 17000]
        );
        PosOrderItem::firstOrCreate(
            ['pos_order_id' => $posOrder1->id, 'item_name' => 'Kopi Robusta Cikuray Roast 200g (Mang Asep)'],
            ['item_type' => 'STORE_KONSINYASI', 'reference_id' => 6, 'qty' => 1, 'unit_price' => 38000, 'subtotal' => 38000, 'cost_price' => 28000, 'mitra_id' => $mitra1->id]
        );
        PosOrderItem::firstOrCreate(
            ['pos_order_id' => $posOrder1->id, 'item_name' => 'Sewa Tenda Dome 4P Great Outdoor'],
            ['item_type' => 'RENTAL', 'reference_id' => 1, 'qty' => 1, 'unit_price' => 45000, 'subtotal' => 45000, 'cost_price' => 0]
        );
        PosOrderItem::firstOrCreate(
            ['pos_order_id' => $posOrder1->id, 'item_name' => 'Jaminan Sampah (Eco-Deposit)'],
            ['item_type' => 'ECO_DEPOSIT', 'reference_id' => $sampleBooking1->id, 'qty' => 1, 'unit_price' => 20000, 'subtotal' => 20000, 'cost_price' => 0]
        );

        // 9. Call Specialized Domain Seeders (Checkpoints, Ojek, Analytics)
        $this->call([
            CheckpointSeeder::class,
            OjekSeeder::class,
            AnalyticsDataSeeder::class,
        ]);
    }
}
