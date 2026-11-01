<?php

namespace Tests\Feature;

use App\Modules\POS\Models\PosOrder;
use App\Modules\Simaksi\Models\SimaksiBooking;
use App\Modules\Store\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreAndPosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_store_catalog_loads(): void
    {
        $response = $this->get(route('store.index'));
        $response->assertStatus(200);
        $response->assertSee('Perlengkapan');
        $response->assertSee('Produk Lokal');
        $response->assertSee('Gas Butane Hi-Cook');
        $response->assertSee('Mang Asep');
    }

    public function test_preorder_submission(): void
    {
        $product = Product::first();

        $payload = [
            'customer_name' => 'Pendaki Preorder',
            'customer_phone' => '081234567890',
            'payment_method' => 'QRIS',
            'items' => [
                ['product_id' => $product->id, 'qty' => 2]
            ]
        ];

        $response = $this->post(route('store.preorder'), $payload);
        $order = PosOrder::where('customer_name', 'Pendaki Preorder')->first();
        $this->assertNotNull($order);
        $this->assertTrue($order->is_preorder_pickup);

        $response->assertRedirect(route('store.success', ['invoice' => $order->invoice_number]));
    }

    public function test_pos_checkout_and_pickup(): void
    {
        $kasir = \App\Models\User::where('role', \App\Shared\Enums\UserRole::KASIR)->first();
        $product = Product::first();
        $initialStock = $product->stock;

        $payload = [
            'customer_name' => 'Budi Kasir',
            'payment_method' => 'CASH',
            'paid_amount' => 100000,
            'items' => [
                ['product_id' => $product->id, 'qty' => 1]
            ]
        ];

        $response = $this->actingAs($kasir)->postJson(route('pos.checkout'), $payload);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify stock deducted
        $this->assertEquals($initialStock - 1, $product->fresh()->stock);

        // Test pre-order pickup scan
        $booking = SimaksiBooking::first();
        $pickupResponse = $this->actingAs($kasir)->postJson(route('pos.pickup'), ['code' => $booking->booking_code]);
        $pickupResponse->assertStatus(200);
        $pickupResponse->assertJson(['success' => true]);
    }

    public function test_kasir_cannot_checkout_before_opening_shift(): void
    {
        $kasir = \App\Models\User::where('role', \App\Shared\Enums\UserRole::KASIR)->first();
        // Close any existing open shift for this kasir
        \App\Modules\POS\Models\CashierShift::where('cashier_user_id', $kasir->id)->update(['status' => 'CLOSED']);

        $product = Product::first();
        $payload = [
            'customer_name' => 'Pendaki Tanpa Shift',
            'payment_method' => 'CASH',
            'paid_amount' => 50000,
            'items' => [
                ['product_id' => $product->id, 'qty' => 1]
            ]
        ];

        // Attempting checkout without active shift should fail with 422
        $response = $this->actingAs($kasir)->postJson(route('pos.checkout'), $payload);
        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'requires_shift' => true,
        ]);

        // Now open shift with formatted or numeric value
        $openShiftResponse = $this->actingAs($kasir)->post(route('pos.shift.open'), [
            'opening_cash' => 500000,
        ]);
        $openShiftResponse->assertSessionHas('success');

        // Now checkout should succeed
        $responseAfterOpen = $this->actingAs($kasir)->postJson(route('pos.checkout'), $payload);
        $responseAfterOpen->assertStatus(200);
        $responseAfterOpen->assertJson(['success' => true]);
    }

    public function test_shift_handles_formatted_indonesian_currency_input(): void
    {
        $kasir = \App\Models\User::where('role', \App\Shared\Enums\UserRole::KASIR)->first();
        \App\Modules\POS\Models\CashierShift::where('cashier_user_id', $kasir->id)->update(['status' => 'CLOSED']);

        // Test opening shift with Indonesian currency format "500.000"
        $response = $this->actingAs($kasir)->post(route('pos.shift.open'), [
            'opening_cash' => '500.000',
        ]);
        $response->assertSessionHas('success');

        $shift = \App\Modules\POS\Models\CashierShift::where('cashier_user_id', $kasir->id)
            ->where('status', 'OPEN')
            ->first();
        $this->assertNotNull($shift);
        $this->assertEquals(500000.0, (float) $shift->opening_cash);

        // Test closing shift with formatted physical cash "650.000"
        $closeResponse = $this->actingAs($kasir)->post(route('pos.shift.close', $shift->id), [
            'closing_cash_physical' => '650.000',
            'notes' => 'Tutup kasir shift sore',
        ]);
        $closeResponse->assertSessionHas('success');

        $shift->refresh();
        $this->assertEquals('CLOSED', $shift->status);
        $this->assertEquals(650000.0, (float) $shift->closing_cash_physical);
    }

    public function test_currency_input_component_renders_on_pos_page(): void
    {
        $kasir = \App\Models\User::where('role', \App\Shared\Enums\UserRole::KASIR)->first();
        $response = $this->actingAs($kasir)->get(route('pos.index'));
        $response->assertStatus(200);
        $response->assertSee('data-currency-display', false);
        $response->assertSee('x-currency', false);
        $response->assertSee('500.000');
    }
}
