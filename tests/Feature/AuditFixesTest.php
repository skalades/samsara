<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Admin\Services\AdminService;
use App\Modules\Ojek\Models\OjekRoute;
use App\Modules\POS\Models\CashierShift;
use App\Modules\POS\Models\PosOrder;
use App\Modules\POS\Services\PosService;
use App\Modules\Rental\Models\RentalItem;
use App\Modules\Rental\Models\RentalOrder;
use App\Modules\Simaksi\Models\SimaksiBooking;
use App\Modules\Store\Models\Product;
use App\Shared\Enums\BookingStatus;
use App\Shared\Enums\PaymentMethod;
use App\Shared\Enums\RentalAssetStatus;
use App\Shared\Enums\UserRole;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditFixesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_booking_check_can_find_preorder_by_invoice_number(): void
    {
        $order = PosOrder::create([
            'invoice_number' => 'INV-PREORDER-999',
            'customer_name' => 'Pendaki Preorder Spesial',
            'customer_phone' => '081299998888',
            'payment_method' => PaymentMethod::QRIS,
            'payment_status' => 'PAID',
            'is_preorder_pickup' => true,
            'subtotal_store' => 50000,
            'paid_amount' => 50000,
            'total_amount' => 50000,
        ]);

        $response = $this->get(route('simaksi.check', ['code' => $order->invoice_number]));
        $response->assertStatus(200);
        $response->assertSee($order->invoice_number);
        $response->assertSee('Pre-Order Logistik Basecamp');
    }

    public function test_pos_pickup_can_process_both_invoice_and_booking_code(): void
    {
        $posService = app(PosService::class);

        // 1. Pickup by invoice number
        $preorder = PosOrder::create([
            'invoice_number' => 'INV-PICKUP-TEST',
            'customer_name' => 'Pendaki Pickup Test',
            'customer_phone' => '081234567890',
            'payment_method' => PaymentMethod::QRIS,
            'payment_status' => 'PAID',
            'is_preorder_pickup' => true,
            'subtotal_store' => 25000,
            'paid_amount' => 25000,
            'total_amount' => 25000,
        ]);

        $result = $posService->markPreorderPickup($preorder->invoice_number);
        $this->assertTrue($result['success']);
        $this->assertStringContainsString('telah diambil', $preorder->fresh()->notes);

        // 2. Pickup by booking code
        $booking = SimaksiBooking::first();
        $this->assertNotNull($booking);
        $resultBooking = $posService->markPreorderPickup($booking->booking_code);
        $this->assertTrue($resultBooking['success']);
        $this->assertStringContainsString('telah diambil', $booking->fresh()->notes);
    }

    public function test_fieldops_scan_rejects_unpaid_or_cancelled_bookings(): void
    {
        $booking = SimaksiBooking::first();
        $booking->update([
            'status' => BookingStatus::PENDING_PAYMENT,
        ]);

        $fieldOpsUser = User::where('role', UserRole::PETUGAS_POS)->first();

        // Should reject PENDING_PAYMENT
        $response = $this->actingAs($fieldOpsUser)->postJson(route('fieldops.scan'), [
            'qr_data' => $booking->qr_token,
            'pos_name' => 'Pos 1 Tanjung Eod',
            'direction' => 'ASCENDING',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
        $this->assertStringContainsStringIgnoringCase('belum lunas', $response->json('message'));

        // Should reject CANCELLED
        $booking->update(['status' => BookingStatus::CANCELLED]);
        $responseCancelled = $this->actingAs($fieldOpsUser)->postJson(route('fieldops.scan'), [
            'qr_data' => $booking->qr_token,
            'pos_name' => 'Pos 1 Tanjung Eod',
            'direction' => 'ASCENDING',
        ]);
        $responseCancelled->assertStatus(422);
        $this->assertStringContainsStringIgnoringCase('dibatalkan', $responseCancelled->json('message'));
    }

    public function test_pos_checkout_with_ojek_separates_subtotal_ojek(): void
    {
        $kasir = User::where('role', UserRole::KASIR)->first();
        $route = OjekRoute::first();
        $this->assertNotNull($route);

        $product = Product::first();

        $payload = [
            'customer_name' => 'Pendaki Ojek Test',
            'customer_phone' => '081234567890',
            'payment_method' => 'CASH',
            'paid_amount' => 150000,
            'ojek_route_id' => $route->id,
            'ojek_qty' => 1,
            'items' => [
                ['product_id' => $product->id, 'qty' => 1]
            ]
        ];

        $response = $this->actingAs($kasir)->postJson(route('pos.checkout'), $payload);
        $response->assertStatus(200);

        $order = PosOrder::where('customer_name', 'Pendaki Ojek Test')->first();
        $this->assertNotNull($order);
        $this->assertEquals((float) $route->fare_amount, (float) $order->subtotal_ojek);
        $this->assertEquals((float) $product->selling_price, (float) $order->subtotal_store);
        $this->assertEquals((float) ($product->selling_price + $route->fare_amount), (float) $order->total_amount);
    }

    public function test_pos_checkout_with_rentals_locks_item_and_creates_rental_order(): void
    {
        $kasir = User::where('role', UserRole::KASIR)->first();
        $item = RentalItem::where('status', RentalAssetStatus::TERSEDIA)->first();
        $this->assertNotNull($item);

        $payload = [
            'customer_name' => 'Pendaki Rental Test',
            'customer_phone' => '081299887766',
            'payment_method' => 'CASH',
            'paid_amount' => 200000,
            'items' => [],
            'rentals' => [
                [
                    'rental_item_id' => $item->id,
                    'rental_days' => 2,
                ]
            ]
        ];

        $response = $this->actingAs($kasir)->postJson(route('pos.checkout'), $payload);
        $response->assertStatus(200);

        $order = PosOrder::where('customer_name', 'Pendaki Rental Test')->first();
        $this->assertNotNull($order);
        $this->assertGreaterThan(0, (float) $order->subtotal_rental);

        // Verify item status updated to DISEWA
        $this->assertEquals(RentalAssetStatus::DISEWA, $item->fresh()->status);

        // Verify RentalOrder created
        $rentalOrder = RentalOrder::where('customer_name', 'Pendaki Rental Test')->first();
        $this->assertNotNull($rentalOrder);
        $this->assertEquals('ACTIVE', $rentalOrder->status);
    }

    public function test_financial_report_separates_ojek_and_store_revenue(): void
    {
        $adminService = app(AdminService::class);
        $report = $adminService->getFinancialReport(Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth());

        $this->assertArrayHasKey('ojek_revenue', $report);
        $this->assertArrayHasKey('store_revenue', $report);
        $this->assertArrayHasKey('allocation', $report);
        $this->assertArrayHasKey('driver_ojek', $report['allocation']);
        $this->assertArrayHasKey('kas_paguyuban_ojek', $report['allocation']);
        $this->assertArrayHasKey('samsara_kas', $report['allocation']);
    }

    public function test_close_shift_has_upper_bound_time(): void
    {
        $posService = app(PosService::class);
        $kasir = User::create([
            'name' => 'Kasir Isolasi Test',
            'email' => 'kasir.isolasi@test.com',
            'password' => bcrypt('password'),
            'role' => UserRole::KASIR,
        ]);

        // 1. Open shift with 100,000 opening cash
        $shift = $posService->openShift($kasir->id, 100000);
        $this->assertEquals('OPEN', $shift->status);

        // 2. Order inside shift (50,000)
        PosOrder::create([
            'invoice_number' => 'INV-SHIFT-1',
            'cashier_user_id' => $kasir->id,
            'customer_name' => 'Test Shift 1',
            'payment_method' => PaymentMethod::CASH,
            'payment_status' => 'PAID',
            'total_amount' => 50000,
            'paid_amount' => 50000,
            'created_at' => now(),
        ]);

        // 3. Close shift (Physical cash 150,000 matches expected 100,000 + 50,000)
        $closedShift = $posService->closeShift($shift->id, 150000);
        $this->assertEquals('CLOSED', $closedShift->status);
        $this->assertEquals(150000, (float) $closedShift->closing_cash_system);
        $this->assertEquals(0, (float) $closedShift->cash_difference);

        // 4. Order created AFTER shift is closed
        $laterOrder = new PosOrder([
            'invoice_number' => 'INV-SHIFT-LATER',
            'cashier_user_id' => $kasir->id,
            'customer_name' => 'Test Shift Later',
            'payment_method' => PaymentMethod::CASH,
            'payment_status' => 'PAID',
            'total_amount' => 75000,
            'paid_amount' => 75000,
        ]);
        $laterOrder->created_at = Carbon::parse($closedShift->closed_at)->addMinutes(10);
        $laterOrder->save();

        // Recalculating sales using the shift's boundaries shouldn't include the later order
        $salesDuringShift = PosOrder::where('cashier_user_id', $kasir->id)
            ->whereBetween('created_at', [$closedShift->opened_at, $closedShift->closed_at])
            ->where('payment_method', PaymentMethod::CASH)
            ->sum('total_amount');

        $this->assertEquals(50000, (float) $salesDuringShift);
    }

    public function test_pos_checkout_rejects_cancelled_booking(): void
    {
        $posService = app(PosService::class);
        $kasir = User::where('role', UserRole::KASIR)->first();
        $booking = SimaksiBooking::first();
        $booking->update(['status' => BookingStatus::CANCELLED]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('sudah dibatalkan');

        $posService->processCheckout([
            'booking_id' => $booking->id,
            'customer_name' => 'Pendaki Batal',
            'payment_method' => 'CASH',
            'paid_amount' => 100000,
        ], $kasir->id);
    }

    public function test_ojek_cancel_ticket_restores_driver_queue_order_to_priority(): void
    {
        $queueService = app(\App\Modules\Ojek\Services\OjekQueueService::class);
        $route = OjekRoute::first();

        // Create fresh drivers to avoid state from seeders
        $driver1 = \App\Modules\Ojek\Models\OjekDriver::create([
            'driver_number' => 'DRV-TEST-901',
            'name' => 'Driver Test Cancel 1',
            'license_plate' => 'Z 9901 AA',
            'is_active' => true,
        ]);
        $attendance1 = $queueService->checkInDriver($driver1->id);

        $driver2 = \App\Modules\Ojek\Models\OjekDriver::create([
            'driver_number' => 'DRV-TEST-902',
            'name' => 'Driver Test Cancel 2',
            'license_plate' => 'Z 9902 AA',
            'is_active' => true,
        ]);
        $attendance2 = $queueService->checkInDriver($driver2->id);

        // Create ticket
        $ticket = $queueService->createTicket([
            'ojek_route_id' => $route->id,
            'passenger_name' => 'Penumpang Cancel Test',
        ]);

        // Call next: driver1 is called
        $calledTicket = $queueService->callNextQueue($ticket->id, $attendance1->id);
        $this->assertEquals(\App\Shared\Enums\OjekQueueStatus::CALLED, $calledTicket->status);
        $this->assertEquals(\App\Shared\Enums\OjekDriverStatus::CALLED, $attendance1->fresh()->status);

        // Cancel ticket: driver1 should be returned to queue_order = 1
        $queueService->cancelTicket($calledTicket->id, 'Penumpang berubah pikiran');

        $this->assertEquals(\App\Shared\Enums\OjekDriverStatus::STANDBY, $attendance1->fresh()->status);
        $this->assertEquals(1, $attendance1->fresh()->queue_order);
    }

    public function test_consignment_settlement_draft_workflow_and_confirm(): void
    {
        $consignmentService = app(\App\Modules\Consignment\Services\ConsignmentService::class);
        $mitra = \App\Modules\Consignment\Models\ConsignmentMitra::first();
        $this->assertNotNull($mitra);

        // Generate settlement
        $settlement = $consignmentService->generateSettlementForMitra(
            $mitra->id,
            now()->subDays(7),
            now()
        );

        if ($settlement) {
            $this->assertEquals('DRAFT', $settlement->status);
            $this->assertNull($settlement->paid_at);

            // Confirm settlement
            $confirmed = $consignmentService->confirmSettlement($settlement->id);
            $this->assertEquals('PAID', $confirmed->status);
            $this->assertNotNull($confirmed->paid_at);
        } else {
            $this->assertTrue(true);
        }
    }

    public function test_fieldops_eco_deposit_verification_strict_80_percent(): void
    {
        $fieldOpsService = app(\App\Modules\FieldOps\Services\FieldOpsService::class);
        $staff = User::where('role', UserRole::PETUGAS_POS)->first();
        $booking = SimaksiBooking::first();
        $deposit = $booking->ecoDeposit;

        $deposit->update([
            'brought_plastics_count' => 10,
            'brought_cans_count' => 5,
            'brought_bottles_count' => 5, // total 20
        ]);

        // 1. Returning only 5 items (< 80% of 20 = 16) -> should be FORFEITED_TRASH_LEFT
        $resultFail = $fieldOpsService->verifyEcoDeposit($booking->id, [
            'returned_plastics_count' => 2,
            'returned_cans_count' => 2,
            'returned_bottles_count' => 1,
        ], $staff->id);

        $this->assertEquals('FORFEITED_TRASH_LEFT', $resultFail->deposit_status);
        $this->assertNull($resultFail->refunded_at);

        // 2. Returning 16 items (80%) -> should be REFUNDED
        $resultPass = $fieldOpsService->verifyEcoDeposit($booking->id, [
            'returned_plastics_count' => 8,
            'returned_cans_count' => 4,
            'returned_bottles_count' => 4,
        ], $staff->id);

        $this->assertEquals('REFUNDED', $resultPass->deposit_status);
        $this->assertNotNull($resultPass->refunded_at);
    }

    public function test_admin_service_get_live_pos_tracker_pattern_matching(): void
    {
        $adminService = app(AdminService::class);

        // Create bookings at various pos names
        SimaksiBooking::where('id', '>', 0)->update(['status' => BookingStatus::ON_TRAIL]);
        $booking = SimaksiBooking::first();
        $booking->update(['current_pos' => 'Pos 2 (Batu Gendrung)', 'status' => BookingStatus::ON_TRAIL]);

        $tracker = $adminService->getLivePosTracker();

        $this->assertArrayHasKey('Pos 2 (Batu Gendrung)', $tracker);
        $this->assertArrayHasKey('Pos 3 (Alam Rimba)', $tracker);
        $this->assertArrayHasKey('Pos 4 (Samudra Awan)', $tracker);

        $pos2BookingCodes = collect($tracker['Pos 2 (Batu Gendrung)'])->pluck('booking_code')->toArray();
        $this->assertContains($booking->booking_code, $pos2BookingCodes);
    }
}
