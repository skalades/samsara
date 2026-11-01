<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Simaksi\Models\SimaksiBooking;
use App\Modules\Simaksi\Models\SosAlert;
use App\Modules\Simaksi\Models\TrailSetting;
use App\Shared\Enums\BookingStatus;
use App\Shared\Enums\TrailStatus;
use App\Shared\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FieldOpsAndAdminTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $posUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->adminUser = User::where('role', UserRole::SUPERADMIN)->first();
        $this->posUser = User::where('role', UserRole::PETUGAS_POS)->first();
    }

    public function test_fieldops_scan_updates_checkpoint_and_position(): void
    {
        $booking = SimaksiBooking::first();

        $payload = [
            'qr_data' => $booking->qr_token,
            'pos_name' => 'Pos 3 (Alam Rimba)',
            'direction' => 'ASCENDING',
            'notes' => 'Rombongan sehat melintas'
        ];

        $response = $this->actingAs($this->posUser)->postJson(route('fieldops.scan'), $payload);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertEquals('Pos 3 (Alam Rimba)', $booking->fresh()->current_pos);
    }

    public function test_emergency_sos_alert(): void
    {
        $payload = [
            'reporter_name' => 'Petugas Jalur 2',
            'reporter_phone' => '081234567890',
            'pos_location' => 'Pos 2 (Batu Gendrung)',
            'alert_type' => 'HYPOTHERMIA',
            'description' => '1 orang pendaki drop suhu di dekat shelter'
        ];

        $response = $this->actingAs($this->posUser)->postJson(route('fieldops.sos'), $payload);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('sos_alerts', [
            'pos_location' => 'Pos 2 (Batu Gendrung)',
            'alert_type' => 'HYPOTHERMIA'
        ]);
    }

    public function test_admin_emergency_trail_control(): void
    {
        $payload = [
            'trail_status' => 'WARNING',
            'weather_status' => 'HUJAN_LEBAT',
            'announcement' => 'Waspada angin kencang di punggungan Pos 4.'
        ];

        $response = $this->actingAs($this->adminUser)->post(route('admin.trail-control'), $payload);
        $response->assertRedirect();

        $setting = TrailSetting::current();
        $this->assertEquals(TrailStatus::WARNING, $setting->trail_status);
    }

    public function test_admin_sar_export_page_loads(): void
    {
        $booking = SimaksiBooking::first();
        $response = $this->actingAs($this->adminUser)->get(route('admin.sar-export', $booking->id));
        $response->assertStatus(200);
        $response->assertSee('TIM SAR BASECAMP SAMSARA');
        $response->assertSee($booking->booking_code);
    }
}
