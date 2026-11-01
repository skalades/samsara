<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Simaksi\Models\Checkpoint;
use App\Modules\Simaksi\Models\CheckpointPhoto;
use Illuminate\Database\Seeder;

class CheckpointSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@samsara.id')->first();
        $adminId = $admin ? $admin->id : 1;

        $checkpoints = [
            [
                'code' => 'basecamp',
                'number' => 'BC',
                'title' => 'Basecamp SAMSARA Cigedug',
                'elevation' => '1.245 MDPL',
                'distance' => '0 KM (Titik Mulai)',
                'time_estimate' => 'Start Registrasi',
                'water_info' => 'Melimpah (Kran & Toilet)',
                'water_available' => true,
                'signal_info' => '4G Semua Operator (Kuat)',
                'tent_capacity' => 'Area Transit & Istirahat',
                'terrain' => 'Jalan Aspal & Pelataran Basecamp',
                'default_image' => '/assets/hero-cikuray.jpg',
                'tagline' => 'Pusat Logistik, Musholla, Toilet & Registrasi SIMAKSI',
                'description' => 'Gerbang awal pendakian dengan fasilitas terpadu: pos briefing keselamatan, pengecekan perlengkapan & deposit sampah, parkir kendaraan bermotor berpagar 24 jam, kantin logistik, dan musholla luas.',
                'order' => 1,
                'photos' => [
                    [
                        'title' => 'Pelataran & Fasilitas Basecamp SAMSARA',
                        'description' => 'Titik awal registrasi SIMAKSI, kantin logistik hangat, toilet bersih, dan shelter transit pendaki sebelum pendakian.',
                        'photo_url' => '/assets/hero-cikuray.jpg',
                        'is_primary' => true,
                    ],
                    [
                        'title' => 'Gerbang Utama Jalur Perkebunan Teh Cigedug',
                        'description' => 'Pemandangan asri kebun teh yang menyambut para pendaki di kaki lereng Gunung Cikuray.',
                        'photo_url' => '/assets/pos1-tanjakan-eod.jpg',
                        'is_primary' => false,
                    ],
                ],
            ],
            [
                'code' => 'pos1',
                'number' => 'POS 1',
                'title' => 'Tanjakan EOD (End of Days)',
                'elevation' => '1.424 MDPL',
                'distance' => '±1.5 KM dari Basecamp',
                'time_estimate' => '60 – 90 Menit',
                'water_info' => 'Tidak Ada Sumber Air',
                'water_available' => false,
                'signal_info' => '4G Stabil',
                'tent_capacity' => 'Hanya Shelter Singgah',
                'terrain' => 'Akar Pohon Padat & Batas Kebun Teh',
                'default_image' => '/assets/pos1-tanjakan-eod.jpg',
                'tagline' => 'Tanjakan Tanah Berakar Padat & Titik Adaptasi Fisik',
                'description' => 'EOD adalah pos pertama setelah melewati gerbang kebun teh Cigedug. Rute didominasi tanah padat dengan sulur-sulur akar rapat yang berfungsi sebagai undakan alami. Sangat krusial untuk mengatur ritme pernapasan awal.',
                'order' => 2,
                'photos' => [
                    [
                        'title' => 'Jalur Tanjakan EOD & Formasi Akar Alami',
                        'description' => 'Tanjakan berakar lebat khas vegetasi Cikuray bawah. Sediakan sarung tangan untuk pegangan undakan tanah.',
                        'photo_url' => '/assets/pos1-tanjakan-eod.jpg',
                        'is_primary' => true,
                    ],
                ],
            ],
            [
                'code' => 'pos2',
                'number' => 'POS 2',
                'title' => 'Batu Gendrung',
                'elevation' => '1.682 MDPL',
                'distance' => '±1.3 KM dari Pos 1',
                'time_estimate' => '60 – 75 Menit',
                'water_info' => 'Tidak Ada Sumber Air',
                'water_available' => false,
                'signal_info' => 'Sinyal Terbatas (Edge/3G)',
                'tent_capacity' => '2 Tenda (Darurat)',
                'terrain' => 'Batuan Andesit Vulkanik & Kanopi Teduh',
                'default_image' => '/assets/pos2-batu-gendrung.jpg',
                'tagline' => 'Shelter Alami Batuan Purba Terlindung Angin',
                'description' => 'Dinamai dari formasi batu andesit vulkanik besar menyerupai alat tetabuhan tradisional (gendrung). Pos ini memiliki kanopi hutan teduh yang terlindung dari terpaan angin kencang lereng, cocok untuk evaluasi stamina dan rehidrasi.',
                'order' => 3,
                'photos' => [
                    [
                        'title' => 'Formasi Batuan Purba Batu Gendrung',
                        'description' => 'Shelter batuan andesit alami yang sangat teduh untuk istirahat sejenak dan rehidrasi.',
                        'photo_url' => '/assets/pos2-batu-gendrung.jpg',
                        'is_primary' => true,
                    ],
                ],
            ],
            [
                'code' => 'pos3',
                'number' => 'POS 3',
                'title' => 'Alam Rimba',
                'elevation' => '2.096 MDPL',
                'distance' => '±1.8 KM dari Pos 2',
                'time_estimate' => '90 – 120 Menit',
                'water_info' => 'Ada (50m Turun ke Lembah)',
                'water_available' => true,
                'signal_info' => 'Hampir Tidak Ada',
                'tent_capacity' => '5 – 6 Tenda',
                'terrain' => 'Hutan Lumut Basah (Cloud Forest)',
                'default_image' => '/assets/pos3-alam-rimba.jpg',
                'tagline' => 'Zona Hutan Lumut Magis & Sumber Air Alami Terakhir',
                'description' => 'Memasuki elevasi 2.000 MDPL, suhu udara menurun tajam dan kelembapan meningkat. Dikelilingi pohon cantigi purba berselimut lumut hijau tebal. Terdapat jalur cabang sekitar 50 meter menuju mata air lembah alami (sumber air terakhir sebelum puncak).',
                'order' => 4,
                'photos' => [
                    [
                        'title' => 'Hutan Lumut Rimba & Akses Mata Air Lembah',
                        'description' => 'Kawasan hutan lumut magis berkabut tebal. Titik pengambilan air terakhir sebelum menuju Camp Utama Pos 4.',
                        'photo_url' => '/assets/pos3-alam-rimba.jpg',
                        'is_primary' => true,
                    ],
                ],
            ],
            [
                'code' => 'pos4',
                'number' => 'POS 4',
                'title' => 'Samudra Awan (Camp Utama)',
                'elevation' => '2.257 MDPL',
                'distance' => '±0.8 KM dari Pos 3',
                'time_estimate' => '60 – 80 Menit',
                'water_info' => 'Tidak Ada (Bawa dari Pos 3/BC)',
                'water_available' => false,
                'signal_info' => 'Sinyal Muncul di Punggungan',
                'tent_capacity' => '80+ Tenda (Camp Utama)',
                'terrain' => 'Punggungan Terbuka Menghadap Timur',
                'default_image' => '/assets/pos4-puncak-samsara.jpg',
                'tagline' => 'Bumi Perkemahan Terbaik dengan Panorama Sunrise & Lautan Awan',
                'description' => 'Camp area favorit seluruh pendaki Cikuray via Cigedug. Lokasinya bertengger di punggungan terbuka dengan panorama langsung matahari terbit dan hamparan samudra awan di atas Garut. Terdapat Posko Ranger SAMSARA untuk keamanan pendaki.',
                'order' => 5,
                'photos' => [
                    [
                        'title' => 'Panorama Sunrise & Lautan Awan di Pos 4',
                        'description' => 'Pemandangan megah matahari terbit di atas lautan awan dari area camp utama Samudra Awan.',
                        'photo_url' => '/assets/pos4-puncak-samsara.jpg',
                        'is_primary' => true,
                    ],
                    [
                        'title' => 'Area Camp Tenda Punggungan Pos 4',
                        'description' => 'Kapasitas lebih dari 80 tenda dengan kontur punggungan datar yang nyaman.',
                        'photo_url' => '/assets/hero-cikuray.jpg',
                        'is_primary' => false,
                    ],
                ],
            ],
            [
                'code' => 'puncak',
                'number' => 'PUNCAK',
                'title' => 'Puncak Cikuray (Atap Garut)',
                'elevation' => '2.821 MDPL',
                'distance' => '±1.4 KM dari Pos 4',
                'time_estimate' => '1.5 – 2 Jam (Summit Attack)',
                'water_info' => 'Tidak Ada Sumber Air',
                'water_available' => false,
                'signal_info' => '4G Kuat di Titik Puncak',
                'tent_capacity' => 'Area Terbatas (Angin Kencang)',
                'terrain' => 'Tanjakan Terbuka & Batuan Kering',
                'default_image' => '/assets/hero-cikuray.jpg',
                'tagline' => 'Puncak Kerucut Tertinggi dengan Lanskap 360 Derajat',
                'description' => 'Titik tertinggi di Kabupaten Garut dengan tugu pemancar bersejarah. Menyajikan lanskap spektakuler 360° menghadap Gunung Papandayan, Gunung Guntur, dan Galunggung di atas karpet awan putih.',
                'order' => 6,
                'photos' => [
                    [
                        'title' => 'Tugu Pemancar Puncak Cikuray 2.821 MDPL',
                        'description' => 'Atap tertinggi Garut dengan panorama spektakuler 360 derajat menghadap Gunung Papandayan dan Guntur.',
                        'photo_url' => '/assets/hero-cikuray.jpg',
                        'is_primary' => true,
                    ],
                ],
            ],
        ];

        foreach ($checkpoints as $cpData) {
            $photos = $cpData['photos'] ?? [];
            unset($cpData['photos']);

            $checkpoint = Checkpoint::updateOrCreate(
                ['code' => $cpData['code']],
                $cpData
            );

            foreach ($photos as $p) {
                CheckpointPhoto::updateOrCreate(
                    [
                        'checkpoint_id' => $checkpoint->id,
                        'title' => $p['title'],
                    ],
                    [
                        'description' => $p['description'],
                        'photo_url' => $p['photo_url'],
                        'is_primary' => $p['is_primary'],
                        'uploaded_by' => $adminId,
                    ]
                );
            }
        }
    }
}
