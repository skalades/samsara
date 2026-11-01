<?php

namespace App\Modules\Simaksi\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Simaksi\Models\TrailSetting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicTrailMapController extends Controller
{
    /**
     * Display the dedicated public Trail Map & GPS Track page.
     */
    public function index(Request $request): View
    {
        $trailSetting = TrailSetting::current();
        $targetPos = $request->query('pos', 'all');

        $trailStats = [
            'trail_name' => 'Jalur Pendakian Gunung Cikuray via Cigedug (SAMSARA)',
            'summit_elevation' => 2821,
            'basecamp_elevation' => 1245,
            'elevation_gain' => 1576,
            'total_distance_km' => 6.8,
            'estimated_time' => '4.5 - 6 Jam',
            'difficulty' => 'Menantang & Bergradien Terjal',
            'gps_points_count' => 836,
            'water_points' => 'Basecamp (Kran Air Bersih) & Pos 3 (Mata Air Lembah ±50m)',
            'camp_areas' => 'Pos 4 Samudra Awan (Kapasitas 80+ Tenda) & Basecamp SAMSARA',
        ];

        $checkpoints = [
            [
                'id' => 'basecamp',
                'number' => 'BC',
                'name' => 'Basecamp SAMSARA Cigedug',
                'elevation' => 1245,
                'distance' => '0 KM (Titik Registrasi & Briefing)',
                'time' => 'Start',
                'water' => 'Melimpah (Kran & Toilet)',
                'waterAvailable' => true,
                'signal' => '4G Semua Operator (Kuat)',
                'tentCapacity' => 'Area Transit, Aula & Homestay',
                'terrain' => 'Jalan Aspal & Pelataran Basecamp Berpagar',
                'image' => '/assets/hero-cikuray.jpg',
                'tagline' => 'Pusat Logistik, Musholla, Toilet & Registrasi SIMAKSI',
                'desc' => 'Gerbang resmi pendakian Cikuray dengan fasilitas terpadu: loket registrasi, verifikasi barcode SIMAKSI, kantin logistik, parkir aman 24 jam, dan pos briefing keselamatan.',
                'lat' => -7.332021,
                'lng' => 107.809948,
            ],
            [
                'id' => 'pos1',
                'number' => 'POS 1',
                'name' => 'Tanjakan EOD (End of Days)',
                'elevation' => 1424,
                'distance' => '±1.5 KM dari Basecamp',
                'time' => '60 - 90 Menit',
                'water' => 'Tidak Ada Sumber Air',
                'waterAvailable' => false,
                'signal' => '4G Stabil',
                'tentCapacity' => 'Shelter Singgah (Bukan Camp)',
                'terrain' => 'Tanah Padat & Akar Rapat Batas Kebun Teh',
                'image' => '/assets/pos1-tanjakan-eod.jpg',
                'tagline' => 'Trek Akar Padat & Titik Adaptasi Fisik Awal',
                'desc' => 'Titik transisi dari hamparan kebun teh Cigedug memasuki batas hutan heterogen. Jalur didominasi undakan akar alami dengan elevasi konsisten menanjak tanpa bonus datar.',
                'lat' => -7.331786,
                'lng' => 107.825109,
            ],
            [
                'id' => 'pos2',
                'number' => 'POS 2',
                'name' => 'Batu Gendrung',
                'elevation' => 1682,
                'distance' => '±1.3 KM dari Pos 1',
                'time' => '60 - 75 Menit',
                'water' => 'Tidak Ada Sumber Air',
                'waterAvailable' => false,
                'signal' => 'Edge / 3G Terbatas',
                'tentCapacity' => '2 Tenda (Darurat)',
                'terrain' => 'Formasi Batuan Andesit Purba & Kanopi Hutan Rindang',
                'image' => '/assets/pos2-batu-gendrung.jpg',
                'tagline' => 'Shelter Alami Batuan Purba Terlindung Angin',
                'desc' => 'Dinamai dari formasi batu andesit vulkanik besar menyerupai alat tetabuhan gendrung. Tempat istirahat ideal yang terlindung dari terpaan angin lereng.',
                'lat' => -7.329408,
                'lng' => 107.836591,
            ],
            [
                'id' => 'pos3',
                'number' => 'POS 3',
                'name' => 'Alam Rimba (Mata Air Terakhir)',
                'elevation' => 2096,
                'distance' => '±1.8 KM dari Pos 2',
                'time' => '90 - 120 Menit',
                'water' => 'Ada (Turun ±50m ke Lembah)',
                'waterAvailable' => true,
                'signal' => 'Sinyal Hilang / Sangat Lemah',
                'tentCapacity' => '5 - 6 Tenda',
                'terrain' => 'Hutan Lumut Basah (Cloud Forest) & Pohon Cantigi Tua',
                'image' => '/assets/pos3-alam-rimba.jpg',
                'tagline' => 'Zona Hutan Lumut Magis & Sumber Air Alami Terakhir',
                'desc' => 'Memasuki zona cloud forest bersuhu sejuk berkabut. Dikelilingi pohon cantigi berselimut lumut hijau tebal. Terdapat percabangan menuju sumber mata air alami terakhir sebelum puncak.',
                'lat' => -7.327355,
                'lng' => 107.847455,
            ],
            [
                'id' => 'pos4',
                'number' => 'POS 4',
                'name' => 'Samudra Awan (Camp Area Utama)',
                'elevation' => 2257,
                'distance' => '±0.8 KM dari Pos 3',
                'time' => '60 - 80 Menit',
                'water' => 'Tidak Ada (Wajib Ambil di Pos 3)',
                'waterAvailable' => false,
                'signal' => 'Sinyal Muncul di Punggungan Terbuka',
                'tentCapacity' => '80+ Tenda (Camp Utama)',
                'terrain' => 'Punggungan Terbuka Menghadap Timur & Garut Kota',
                'image' => '/assets/pos4-puncak-samsara.jpg',
                'tagline' => 'Bumi Perkemahan Terfavorit dengan Panorama Samudra Awan',
                'desc' => 'Camp area terfavorit pendaki Gunung Cikuray. Terbentang di punggungan terbuka dengan lanskap matahari terbit dan hamparan awan spektakuler. Terdapat pos pantau Ranger SAMSARA.',
                'lat' => -7.324812,
                'lng' => 107.854219,
            ],
            [
                'id' => 'puncak',
                'number' => 'PUNCAK',
                'name' => 'Puncak Cikuray (Atap Garut)',
                'elevation' => 2821,
                'distance' => '±1.4 KM dari Pos 4',
                'time' => '1.5 - 2 Jam (Summit Attack)',
                'water' => 'Tidak Ada Sumber Air',
                'waterAvailable' => false,
                'signal' => '4G Kuat di Titik Puncak',
                'tentCapacity' => 'Terbatas (Bukan Rekomendasi Camp Karena Angin)',
                'terrain' => 'Tanjakan Terbuka, Batuan Kering & Tugu Puncak',
                'image' => '/assets/hero-cikuray.jpg',
                'tagline' => 'Puncak Kerucut Sempurna 2.821 MDPL Lanskap 360 Derajat',
                'desc' => 'Titik tertinggi di Kabupaten Garut dengan tugu pemancar bersejarah. Menyajikan pemandangan 360° menghadap Gunung Papandayan, Guntur, Galunggung, dan bentang samudra Priangan Timur.',
                'lat' => -7.320491,
                'lng' => 107.863112,
            ],
        ];

        return view('trail.index', compact('trailSetting', 'trailStats', 'checkpoints', 'targetPos'));
    }
}
