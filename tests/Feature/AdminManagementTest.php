<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Rental\Models\RentalItem;
use App\Modules\Simaksi\Models\TrailSetting;
use App\Modules\Store\Models\Product;
use App\Shared\Enums\InventoryType;
use App\Shared\Enums\ProductCategory;
use App\Shared\Enums\RentalAssetStatus;
use App\Shared\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }
    public function test_superadmin_can_access_users_management_and_create_kasir(): void
    {
        $superadmin = User::where('role', UserRole::SUPERADMIN->value)->first();
        if (!$superadmin) {
            $superadmin = User::create([
                'name' => 'Superadmin Test',
                'email' => 'admin_test@samsara.id',
                'password' => bcrypt('password'),
                'role' => UserRole::SUPERADMIN,
            ]);
        }

        $response = $this->actingAs($superadmin)->get(route('admin.users.index'));
        $response->assertStatus(200);
        $response->assertSee('Kelola Akun Staf');

        // Test create kasir
        $storeResponse = $this->actingAs($superadmin)->post(route('admin.users.store'), [
            'name' => 'Kasir Test Baru',
            'email' => 'kasir_test_' . time() . '@samsara.id',
            'password' => 'password123',
            'role' => UserRole::KASIR->value,
            'phone' => '081234567890',
            'assigned_pos' => 'Basecamp',
        ]);

        $storeResponse->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', ['name' => 'Kasir Test Baru']);
    }

    public function test_superadmin_can_access_inventory_and_create_product_and_rental(): void
    {
        $superadmin = User::where('role', UserRole::SUPERADMIN->value)->first();

        $response = $this->actingAs($superadmin)->get(route('admin.inventory.index'));
        $response->assertStatus(200);
        $response->assertSee('Master Inventaris: Toko');

        // Test create product
        $sku = 'TEST-GAS-' . time();
        $pResponse = $this->actingAs($superadmin)->post(route('admin.inventory.products.store'), [
            'sku' => $sku,
            'name' => 'Gas Test Canister',
            'category' => ProductCategory::GAS_BAHAN_BAKAR->value,
            'inventory_type' => InventoryType::BELI_PUTUS->value,
            'cost_price' => 15000,
            'selling_price' => 22000,
            'stock' => 20,
            'minimum_stock_alert' => 5,
            'is_preorder_available' => 1,
            'active' => 1,
        ]);
        $pResponse->assertRedirect(route('admin.inventory.index', ['tab' => 'products']));
        $this->assertDatabaseHas('products', ['sku' => $sku]);

        // Test create rental item
        $assetCode = 'TEST-TND-' . time();
        $rResponse = $this->actingAs($superadmin)->post(route('admin.inventory.rentals.store'), [
            'asset_code' => $assetCode,
            'name' => 'Tenda Dome Test 4P',
            'category' => 'TENDA',
            'rental_rate_per_day' => 45000,
            'deposit_rate' => 50000,
            'status' => RentalAssetStatus::TERSEDIA->value,
            'condition_notes' => 'Kondisi uji coba',
        ]);
        $rResponse->assertRedirect(route('admin.inventory.index', ['tab' => 'rentals']));
        $this->assertDatabaseHas('rental_items', ['asset_code' => $assetCode]);
    }

    public function test_superadmin_can_access_and_update_settings(): void
    {
        $superadmin = User::where('role', UserRole::SUPERADMIN->value)->first();

        $response = $this->actingAs($superadmin)->get(route('admin.settings.index'));
        $response->assertStatus(200);
        $response->assertSee('Konfigurasi Tarif');

        $updateResponse = $this->actingAs($superadmin)->post(route('admin.settings.update'), [
            'ticket_fee_per_person' => 30000,
            'eco_deposit_fee' => 25000,
            'insurance_fee' => 5000,
            'pad_desa_fee' => 5000,
            'trail_status' => 'OPEN',
            'weather_status' => 'CERAH',
            'announcement' => 'Jalur Buka Normal Pengujian.',
        ]);

        $updateResponse->assertRedirect(route('admin.settings.index'));
        $this->assertDatabaseHas('trail_settings', [
            'ticket_fee_per_person' => 30000,
            'eco_deposit_fee' => 25000,
        ]);
    }
}
