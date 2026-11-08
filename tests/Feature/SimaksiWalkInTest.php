<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\POS\Models\PosOrder;
use App\Modules\Simaksi\Models\DailyQuota;
use App\Modules\Simaksi\Models\SimaksiBooking;
use App\Shared\Enums\BookingStatus;
use App\Shared\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimaksiWalkInTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_kasir_can_search_pending_simaksi_booking(): void
    {
        $kasir = User::where('role', UserRole::KASIR)->first();

        // Ensure there is a pending booking
        $booking = SimaksiBooking::create([
            'booking_code' => 'SMX-261108-TEST',
            'hiking_date' => today()->toDateString(),
            'duration_days' => 2,
            'leader_name' => 'Pendaki Mandiri Onsite',
            'leader_nik' => '3205012345670001',
            'leader_phone' => '081299887766',
            'leader_email' => 'mandiri@test.com',
            'leader_address' => 'Bandung',
            'emergency_contact_name' => 'Keluarga',
            'emergency_contact_phone' => '081299887700',
            'total_hikers' => 2,
            'ticket_fee_per_person' => 25000,
            'total_ticket_amount' => 50000,
            'eco_deposit_amount' => 20000,
            'status' => BookingStatus::PENDING_PAYMENT,
            'payment_method' => \App\Shared\Enums\PaymentMethod::CASH,
            'current_pos' => 'Basecamp',
            'qr_token' => 'QR-TEST-WALKIN-1',
        ]);

        $response = $this->actingAs($kasir)->getJson(route('pos.simaksi.search', ['q' => 'Mandiri']));
        $response->assertStatus(200);
        $response->assertJsonFragment(['booking_code' => 'SMX-261108-TEST']);
        $response->assertJsonFragment(['leader_name' => 'Pendaki Mandiri Onsite']);
    }

    public function test_kasir_can_register_walk_in_simaksi_and_quota_decrements(): void
    {
        $kasir = User::where('role', UserRole::KASIR)->first();

        $hikingDate = today()->toDateString();
        $initialQuota = DailyQuota::whereDate('date', $hikingDate)->first();
        $initialBooked = $initialQuota ? $initialQuota->booked_count : 0;

        $payload = [
            'hiking_date' => $hikingDate,
            'duration_days' => 2,
            'leader_name' => 'Agus Walkin',
            'leader_nik' => '3205011122330001',
            'leader_phone' => '081344556677',
            'emergency_contact_name' => 'Ibu Agus',
            'emergency_contact_phone' => '081344556600',
            'tent_color_details' => 'Tenda Merah Kapasitas 4',
            'members' => [
                [
                    'full_name' => 'Agus Walkin',
                    'nik' => '3205011122330001',
                    'gender' => 'L',
                    'age' => 25,
                    'medical_history' => 'Sehat',
                ],
                [
                    'full_name' => 'Bambang Walkin',
                    'nik' => '3205011122330002',
                    'gender' => 'L',
                    'age' => 26,
                    'medical_history' => 'Asma Ringan',
                ],
            ],
        ];

        $response = $this->actingAs($kasir)->postJson(route('pos.simaksi.walkin'), $payload);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $createdBooking = SimaksiBooking::where('leader_name', 'Agus Walkin')->first();
        $this->assertNotNull($createdBooking);
        $this->assertEquals(2, $createdBooking->total_hikers);
        $this->assertEquals(BookingStatus::PENDING_PAYMENT, $createdBooking->status);

        // Check quota decremented
        $updatedQuota = DailyQuota::whereDate('date', $hikingDate)->first();
        $this->assertEquals($initialBooked + 2, $updatedQuota->booked_count);
    }

    public function test_kasir_can_checkout_pos_with_attached_walk_in_simaksi(): void
    {
        $kasir = User::where('role', UserRole::KASIR)->first();

        // 1. Create walk-in booking
        $walkInPayload = [
            'hiking_date' => today()->toDateString(),
            'duration_days' => 2,
            'leader_name' => 'Dedi Kasir OTS',
            'leader_nik' => '3205019988770001',
            'leader_phone' => '081277665544',
            'emergency_contact_name' => 'Keluarga Dedi',
            'emergency_contact_phone' => '081277665500',
            'members' => [
                [
                    'full_name' => 'Dedi Kasir OTS',
                    'nik' => '3205019988770001',
                    'gender' => 'L',
                    'age' => 28,
                    'medical_history' => 'Sehat',
                ],
            ],
        ];

        $walkInRes = $this->actingAs($kasir)->postJson(route('pos.simaksi.walkin'), $walkInPayload);
        $walkInRes->assertStatus(200);
        $bookingId = $walkInRes->json('booking.id');

        // 2. Checkout via POS
        $checkoutPayload = [
            'customer_name' => 'Dedi Kasir OTS',
            'customer_phone' => '081277665544',
            'booking_id' => $bookingId,
            'payment_method' => 'CASH',
            'paid_amount' => 50000,
        ];

        $checkoutRes = $this->actingAs($kasir)->postJson(route('pos.checkout'), $checkoutPayload);
        $checkoutRes->assertStatus(200);
        $checkoutRes->assertJson(['success' => true]);

        // 3. Verify booking status is now CONFIRMED
        $booking = SimaksiBooking::find($bookingId);
        $this->assertEquals(BookingStatus::CONFIRMED, $booking->status);
        $this->assertNotNull($booking->paid_at);

        // 4. Verify POS order and order items
        $posOrder = PosOrder::where('booking_id', $bookingId)->first();
        $this->assertNotNull($posOrder);
        $this->assertTrue($posOrder->items()->where('item_type', 'SIMAKSI')->exists());
        $this->assertTrue($posOrder->items()->where('item_type', 'ECO_DEPOSIT')->exists());
    }

    public function test_admin_can_register_walk_in_simaksi_with_confirmed_status(): void
    {
        $admin = User::where('role', UserRole::SUPERADMIN)->first();

        $payload = [
            'hiking_date' => today()->toDateString(),
            'duration_days' => 2,
            'leader_name' => 'Rombongan VIP Garut',
            'leader_nik' => '3205014455660001',
            'leader_phone' => '081122334455',
            'emergency_contact_name' => 'Humas Pemda',
            'emergency_contact_phone' => '081122334400',
            'payment_status' => 'CONFIRMED',
            'members' => [
                [
                    'full_name' => 'Rombongan VIP Garut',
                    'nik' => '3205014455660001',
                    'gender' => 'L',
                    'age' => 35,
                    'medical_history' => 'Sehat',
                ],
            ],
        ];

        $response = $this->actingAs($admin)->post(route('admin.bookings.walkin'), $payload);
        $response->assertRedirect(route('admin.bookings.pending'));

        $booking = SimaksiBooking::where('leader_name', 'Rombongan VIP Garut')->first();
        $this->assertNotNull($booking);
        $this->assertEquals(BookingStatus::CONFIRMED, $booking->status);
        $this->assertNotNull($booking->paid_at);
    }
}
