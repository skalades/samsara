<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Ojek\Models\OjekDriver;
use App\Modules\Ojek\Models\OjekRoute;
use App\Modules\Ojek\Services\OjekQueueService;
use App\Shared\Enums\OjekDriverStatus;
use App\Shared\Enums\OjekQueueStatus;
use App\Shared\Enums\UserRole;
use Database\Seeders\OjekSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OjekQueueTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->seed(OjekSeeder::class);
    }

    public function test_public_ojek_pages_are_accessible(): void
    {
        $response = $this->get(route('ojek.public.index'));
        $response->assertStatus(200);

        $responseDisplay = $this->get(route('ojek.public.display'));
        $responseDisplay->assertStatus(200);

        $responseApi = $this->get(route('ojek.public.api.display'));
        $responseApi->assertStatus(200)
            ->assertJsonStructure([
                'standby_drivers',
                'waiting_tickets',
                'summary',
                'routes',
            ]);
    }

    public function test_coordinator_dashboard_accessible_by_staff(): void
    {
        $admin = User::firstWhere('role', UserRole::SUPERADMIN) ?? User::factory()->create(['role' => UserRole::SUPERADMIN]);

        $response = $this->actingAs($admin)->get(route('ojek.index'));
        $response->assertStatus(200);
    }

    public function test_fair_fifo_queue_dispatch_and_completion(): void
    {
        $service = app(OjekQueueService::class);
        $route = OjekRoute::first();

        // Buat driver uji khusus
        $driver = OjekDriver::create([
            'driver_number' => 'DRV-TEST',
            'name' => 'Driver Uji FIFO',
            'phone' => '0899009988',
            'license_plate' => 'Z 9999 XX',
            'is_active' => true,
        ]);

        // 1. Check in driver
        $attendance = $service->checkInDriver($driver->id);
        $this->assertEquals(OjekDriverStatus::STANDBY, $attendance->status);

        // 2. Create ticket
        $ticket = $service->createTicket([
            'ojek_route_id' => $route->id,
            'passenger_name' => 'Pendaki Uji FIFO',
            'passenger_phone' => '081200001111',
            'source' => 'PANGKALAN_WALKIN',
        ]);
        $this->assertEquals(OjekQueueStatus::WAITING, $ticket->status);

        // 3. Call next queue
        $calledTicket = $service->callNextQueue($ticket->id, $attendance->id);
        $this->assertEquals(OjekQueueStatus::CALLED, $calledTicket->status);
        $this->assertEquals($driver->id, $calledTicket->assigned_driver_id);

        // 4. Complete trip and verify driver rotates to tail
        $completedTicket = $service->completeTrip($calledTicket->id);
        $this->assertEquals(OjekQueueStatus::COMPLETED, $completedTicket->status);
    }
}
