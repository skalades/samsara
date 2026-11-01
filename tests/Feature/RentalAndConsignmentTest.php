<?php

namespace Tests\Feature;

use App\Modules\Consignment\Models\ConsignmentMitra;
use App\Modules\Consignment\Services\ConsignmentService;
use App\Modules\Rental\Models\RentalItem;
use App\Modules\Rental\Models\RentalOrder;
use App\Modules\Rental\Services\RentalService;
use App\Shared\Enums\RentalAssetStatus;
use App\Shared\Enums\RentalCondition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RentalAndConsignmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_rental_page_loads_kanban(): void
    {
        $admin = \App\Models\User::where('role', \App\Shared\Enums\UserRole::SUPERADMIN)->first();
        $response = $this->actingAs($admin)->get(route('rental.index'));
        $response->assertStatus(200);
        $response->assertSee('Siklus Aset Sewa Basecamp');
        $response->assertSee('Tersedia di Rak');
        $response->assertSee('Dalam Perbaikan');
    }

    public function test_rental_order_creation_and_qc_return(): void
    {
        $rentalService = app(RentalService::class);
        $item = RentalItem::where('status', RentalAssetStatus::TERSEDIA)->first();

        // Create rental order
        $order = $rentalService->createRentalOrder(
            [
                'customer_name' => 'Pendaki Rental',
                'customer_phone' => '081234567890',
            ],
            [
                ['rental_item_id' => $item->id, 'rental_days' => 2]
            ]
        );

        $this->assertEquals('ACTIVE', $order->status);
        $this->assertEquals(RentalAssetStatus::DISEWA, $item->fresh()->status);

        // Process QC return with damage
        $orderItem = $order->items->first();
        $inspections = [
            $orderItem->id => [
                'condition' => RentalCondition::RUSAK_RINGAN->value,
                'penalty' => 25000,
                'notes' => 'Frame sedikit bengkok'
            ]
        ];

        $updatedOrder = $rentalService->processReturnQC($order->id, $inspections);
        $this->assertEquals('RETURNED_WITH_DAMAGE', $updatedOrder->status);
        $this->assertEquals(25000, $updatedOrder->damage_fee);
        $this->assertEquals(RentalAssetStatus::DALAM_PERBAIKAN, $item->fresh()->status);
    }

    public function test_consignment_settlement_and_whatsapp_slip(): void
    {
        $mitra = ConsignmentMitra::first();
        $consignmentService = app(ConsignmentService::class);

        $response = $this->post(route('consignment.settlement'), [
            'mitra_id' => $mitra->id,
            'period_start' => today()->startOfMonth()->toDateString(),
            'period_end' => today()->toDateString(),
        ]);

        $response->assertSessionHasNoErrors();

        // Test slip display
        $settlement = $mitra->settlements()->first();
        if ($settlement) {
            $slipResponse = $this->get(route('consignment.settlement.show', $settlement->id));
            $slipResponse->assertStatus(200);
            $slipResponse->assertSee('SLIP KONSINYASI');
            $slipResponse->assertSee($mitra->name);

            $waSlipText = $consignmentService->generateWhatsAppSlip($settlement);
            $this->assertStringContainsString('SLIP REKONSILIASI KONSINYASI', $waSlipText);
        }
    }
}
