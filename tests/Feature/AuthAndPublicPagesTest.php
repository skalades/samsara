<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Simaksi\Models\SimaksiBooking;
use App\Shared\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAndPublicPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_public_guest_can_access_landing_page(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('SAMSARA Integrated');
        $response->assertSee('Layanan Mandiri Pendaki');
        $response->assertSee('Login Staf');
    }

    public function test_public_guest_can_access_trail_map_page(): void
    {
        $response = $this->get(route('trail.map'));
        $response->assertStatus(200);
        $response->assertSee('Peta Satelit GPS & Trek Jalur Gunung Cikuray', false);
        $response->assertSee('2.821');
        $response->assertSee('Trek 1: Placemarks Pos');
        $response->assertSee('Trek 2: Track Log GPS Riil');
        $response->assertSee('Trek 3: Log Turun');

        // Check alias route /peta-jalur redirects to /peta
        $aliasResponse = $this->get('/peta-jalur');
        $aliasResponse->assertRedirect(route('trail.map'));
    }

    public function test_public_guest_can_access_rental_catalog(): void
    {
        $response = $this->get(route('rental.catalog'));
        $response->assertStatus(200);
        $response->assertSee('Sewa Perlengkapan Outdoor');
        $response->assertSee('Tersedia di Rak');
        $response->assertSee('SOP & Syarat Sewa', false);
    }

    public function test_public_guest_can_access_booking_check_page(): void
    {
        $response = $this->get(route('simaksi.check'));
        $response->assertStatus(200);
        $response->assertSee('Cek Status Tiket & Pre-Order', false);
        $response->assertSee('Cari E-Ticket & Pesanan', false);
    }

    public function test_public_guest_can_search_and_find_booking_ticket(): void
    {
        $booking = SimaksiBooking::first();
        $response = $this->get(route('simaksi.check', ['code' => $booking->booking_code]));

        $response->assertStatus(200);
        $response->assertSee($booking->booking_code);
        $response->assertSee($booking->leader_name);
        $response->assertSee('Manifest Rombongan');
        $response->assertSee('Tracking Posisi di Jalur Cikuray');
    }

    public function test_public_guest_is_redirected_to_login_when_accessing_protected_admin(): void
    {
        $response = $this->get(route('admin.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_staff_can_view_login_page_with_demo_pills(): void
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
        $response->assertSee('Portal Staf & Petugas', false);
        $response->assertSee('Superadmin');
        $response->assertSee('Kasir Basecamp');
        $response->assertSee('Petugas Pos 1');
    }

    public function test_superadmin_login_redirects_to_admin_index(): void
    {
        $response = $this->post(route('login.post'), [
            'email' => 'admin@samsara.id',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.index'));
        $this->assertAuthenticated();
    }

    public function test_kasir_login_redirects_to_pos_index(): void
    {
        $response = $this->post(route('login.post'), [
            'email' => 'kasir@samsara.id',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('pos.index'));
        $this->assertAuthenticated();
    }

    public function test_petugas_pos_login_redirects_to_fieldops_index(): void
    {
        $response = $this->post(route('login.post'), [
            'email' => 'pos1@samsara.id',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('fieldops.index'));
        $this->assertAuthenticated();
    }

    public function test_staff_login_fails_with_invalid_credentials(): void
    {
        $response = $this->from(route('login'))->post(route('login.post'), [
            'email' => 'admin@samsara.id',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_staff_can_logout(): void
    {
        $user = User::where('role', UserRole::SUPERADMIN)->first();

        $response = $this->actingAs($user)->post(route('logout'));
        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_unauthorized_role_is_forbidden_from_restricted_modules(): void
    {
        $posUser = User::where('role', UserRole::PETUGAS_POS)->first();

        // Petugas Pos cannot access admin panel
        $response = $this->actingAs($posUser)->get(route('admin.index'));
        $response->assertStatus(403);
    }
}
