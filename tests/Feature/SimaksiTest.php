<?php

namespace Tests\Feature;

use App\Modules\Simaksi\Models\DailyQuota;
use App\Modules\Simaksi\Models\SimaksiBooking;
use App\Modules\Simaksi\Models\TrailSetting;
use App\Shared\Enums\BookingStatus;
use App\Shared\Enums\TrailStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimaksiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_home_page_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('SAMSARA Integrated');
        $response->assertSee('STATUS JALUR:');
    }

    public function test_simaksi_calendar_page_loads(): void
    {
        $response = $this->get(route('simaksi.index'));
        $response->assertStatus(200);
        $response->assertSee('SIMAKSI Online Gunung Cikuray');
    }

    public function test_booking_simaksi_successful(): void
    {
        $payload = [
            'hiking_date' => today()->addDays(3)->toDateString(),
            'duration_days' => 2,
            'leader_name' => 'Fajar Santoso',
            'leader_nik' => '3205011997010001',
            'leader_phone' => '081234567890',
            'leader_email' => 'fajar@example.com',
            'leader_address' => 'Bandung, Jawa Barat',
            'emergency_contact_name' => 'Ibu Siti (Ibu)',
            'emergency_contact_phone' => '081298765432',
            'payment_method' => 'QRIS',
            'tent_color_details' => 'Tenda Kuning 4P',
            'members' => [
                [
                    'full_name' => 'Fajar Santoso',
                    'nik' => '3205011997010001',
                    'gender' => 'L',
                    'age' => 27,
                    'phone' => '081234567890',
                    'medical_history' => 'Sehat',
                ],
                [
                    'full_name' => 'Aldi Wijaya',
                    'nik' => '3205011998020002',
                    'gender' => 'L',
                    'age' => 26,
                    'phone' => '081234567891',
                    'medical_history' => 'Tidak ada riwayat',
                ]
            ]
        ];

        $response = $this->post(route('simaksi.store'), $payload);
        $response->assertSessionHasNoErrors();

        $booking = SimaksiBooking::where('leader_name', 'Fajar Santoso')->first();
        $this->assertNotNull($booking);
        $this->assertEquals(2, $booking->total_hikers);
        $this->assertEquals(50000, $booking->total_ticket_amount);
        $this->assertEquals(20000, $booking->eco_deposit_amount);
        $this->assertEquals(BookingStatus::PENDING_PAYMENT, $booking->status);

        $response->assertRedirect(route('simaksi.show', $booking->booking_code));
    }

    public function test_booking_fails_when_trail_is_closed(): void
    {
        TrailSetting::current()->update(['trail_status' => TrailStatus::CLOSED]);

        $payload = [
            'hiking_date' => today()->addDays(2)->toDateString(),
            'duration_days' => 2,
            'leader_name' => 'Testing User',
            'leader_nik' => '3205011997010001',
            'leader_phone' => '081234567890',
            'leader_email' => 'test@example.com',
            'leader_address' => 'Garut',
            'emergency_contact_name' => 'Ibu',
            'emergency_contact_phone' => '081298765432',
            'payment_method' => 'CASH',
            'members' => [
                ['full_name' => 'Testing User', 'nik' => '3205011997010001', 'gender' => 'L', 'age' => 25]
            ]
        ];

        $response = $this->post(route('simaksi.store'), $payload);
        $response->assertSessionHas('error');
    }

    public function test_hiker_can_upload_payment_proof(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $booking = SimaksiBooking::where('status', BookingStatus::PENDING_PAYMENT)->first();
        if (!$booking) {
            $booking = SimaksiBooking::create([
                'booking_code' => 'SMX-TEST-UPLOAD',
                'hiking_date' => today()->addDays(2)->toDateString(),
                'duration_days' => 2,
                'leader_name' => 'Pendaki Upload Test',
                'leader_nik' => '3205011997010099',
                'leader_phone' => '081234567899',
                'leader_email' => 'upload@example.com',
                'leader_address' => 'Bandung',
                'emergency_contact_name' => 'Keluarga',
                'emergency_contact_phone' => '081298765439',
                'total_hikers' => 1,
                'ticket_fee_per_person' => 25000,
                'total_ticket_amount' => 25000,
                'eco_deposit_amount' => 20000,
                'status' => BookingStatus::PENDING_PAYMENT,
                'payment_method' => \App\Shared\Enums\PaymentMethod::TRANSFER,
                'qr_token' => 'QR-TEST-UPLOAD',
            ]);
        }

        $file = \Illuminate\Http\UploadedFile::fake()->image('struk_transfer.jpg');

        $response = $this->post(route('simaksi.upload-proof', $booking->booking_code), [
            'payment_proof' => $file,
        ]);

        $response->assertSessionHas('success');
        $booking->refresh();
        $this->assertTrue($booking->hasPaymentProof());
        $this->assertNotNull($booking->payment_proof_path);
    }

    public function test_admin_can_confirm_payment(): void
    {
        $admin = \App\Models\User::where('role', \App\Shared\Enums\UserRole::SUPERADMIN)->first();
        $booking = SimaksiBooking::where('status', BookingStatus::PENDING_PAYMENT)->first();
        if (!$booking) {
            $booking = SimaksiBooking::create([
                'booking_code' => 'SMX-TEST-CONFIRM',
                'hiking_date' => today()->addDays(2)->toDateString(),
                'duration_days' => 2,
                'leader_name' => 'Pendaki Confirm Test',
                'leader_nik' => '3205011997010098',
                'leader_phone' => '081234567898',
                'leader_email' => 'confirm@example.com',
                'leader_address' => 'Bandung',
                'emergency_contact_name' => 'Keluarga',
                'emergency_contact_phone' => '081298765438',
                'total_hikers' => 1,
                'ticket_fee_per_person' => 25000,
                'total_ticket_amount' => 25000,
                'eco_deposit_amount' => 20000,
                'status' => BookingStatus::PENDING_PAYMENT,
                'payment_method' => \App\Shared\Enums\PaymentMethod::TRANSFER,
                'qr_token' => 'QR-TEST-CONFIRM',
            ]);
        }

        $response = $this->actingAs($admin)->post(route('admin.bookings.confirm', $booking->id));
        $response->assertSessionHas('success');

        $booking->refresh();
        $this->assertEquals(BookingStatus::CONFIRMED, $booking->status);
        $this->assertNotNull($booking->paid_at);
    }
}
