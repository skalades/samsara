<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Consignment\Models\ConsignmentMitra;
use App\Modules\Rental\Models\RentalItem;
use App\Modules\Store\Models\Product;
use App\Shared\Enums\InventoryType;
use App\Shared\Enums\ProductCategory;
use App\Shared\Enums\RentalAssetStatus;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InventoryController extends Controller
{
    /**
     * Tampilkan katalog gabungan Produk Toko & Alat Sewa Outdoor.
     */
    public function index(Request $request): View
    {
        $tab = $request->input('tab', 'products'); // 'products' atau 'rentals'
        $search = $request->input('search', '');

        // 1. Data Produk Toko & Konsinyasi
        $productQuery = Product::with('mitra');
        if ($tab === 'products' && !empty($search)) {
            $productQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        if ($request->filled('category')) {
            $productQuery->where('category', $request->input('category'));
        }
        if ($request->filled('type')) {
            $productQuery->where('inventory_type', $request->input('type'));
        }
        $products = $productQuery->latest()->paginate(15, ['*'], 'p_page')->withQueryString();

        // 2. Data Alat Rental Outdoor
        $rentalQuery = RentalItem::query();
        if ($tab === 'rentals' && !empty($search)) {
            $rentalQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asset_code', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            $rentalQuery->where('status', $request->input('status'));
        }
        $rentals = $rentalQuery->latest()->paginate(15, ['*'], 'r_page')->withQueryString();

        // 3. Ringkasan Statistik
        $stats = [
            'total_products' => Product::count(),
            'low_stock_products' => Product::whereColumn('stock', '<=', 'minimum_stock_alert')->count(),
            'consignment_products' => Product::where('inventory_type', InventoryType::KONSINYASI->value)->count(),
            'total_rentals' => RentalItem::count(),
            'available_rentals' => RentalItem::where('status', RentalAssetStatus::TERSEDIA->value)->count(),
            'rented_rentals' => RentalItem::where('status', RentalAssetStatus::DISEWA->value)->count(),
            'need_qc_rentals' => RentalItem::where('status', RentalAssetStatus::PERLU_QC_CUCI->value)->count(),
        ];

        $mitras = ConsignmentMitra::where('active', true)->orderBy('name')->get();
        $productCategories = ProductCategory::cases();
        $inventoryTypes = InventoryType::cases();
        $rentalStatuses = RentalAssetStatus::cases();

        $rentalCategories = [
            'TENDA' => 'Tenda Dome & Flysheet',
            'CARRIER' => 'Carrier / Ransel Gunung',
            'SLEEPING_BAG' => 'Sleeping Bag & Matras',
            'COOKING_SET' => 'Cooking Set & Kompor Portable',
            'TREKKING_POLE' => 'Trekking Pole & Headlamp',
            'LAINNYA' => 'Perlengkapan Lainnya',
        ];

        return view('admin.inventory.index', compact(
            'tab',
            'search',
            'products',
            'rentals',
            'stats',
            'mitras',
            'productCategories',
            'inventoryTypes',
            'rentalStatuses',
            'rentalCategories'
        ));
    }

    // ==========================================
    // PRODUK TOKO & KONSINYASI
    // ==========================================

    public function storeProduct(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
            'name' => ['required', 'string', 'max:150'],
            'category' => ['required', Rule::enum(ProductCategory::class)],
            'inventory_type' => ['required', Rule::enum(InventoryType::class)],
            'consignment_mitra_id' => ['nullable', 'required_if:inventory_type,KONSINYASI', 'exists:consignment_mitras,id'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock_alert' => ['required', 'integer', 'min:0'],
            'is_preorder_available' => ['nullable', 'boolean'],
            'active' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $validated['is_preorder_available'] = (bool) ($request->has('is_preorder_available'));
            $validated['active'] = (bool) ($request->has('active') ? $request->boolean('active') : true);

            if ($validated['inventory_type'] === InventoryType::BELI_PUTUS->value) {
                $validated['consignment_mitra_id'] = null;
            }

            $product = Product::create($validated);

            return redirect()->route('admin.inventory.index', ['tab' => 'products'])
                ->with('success', "Produk \"{$product->name}\" ({$product->sku}) berhasil ditambahkan ke katalog!");
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal menambahkan produk: ' . $e->getMessage());
        }
    }

    public function updateProduct(Request $request, int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:50', Rule::unique('products')->ignore($product->id)],
            'name' => ['required', 'string', 'max:150'],
            'category' => ['required', Rule::enum(ProductCategory::class)],
            'inventory_type' => ['required', Rule::enum(InventoryType::class)],
            'consignment_mitra_id' => ['nullable', 'required_if:inventory_type,KONSINYASI', 'exists:consignment_mitras,id'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock_alert' => ['required', 'integer', 'min:0'],
            'is_preorder_available' => ['nullable', 'boolean'],
            'active' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $validated['is_preorder_available'] = (bool) ($request->has('is_preorder_available'));
            $validated['active'] = (bool) ($request->has('active'));

            if ($validated['inventory_type'] === InventoryType::BELI_PUTUS->value) {
                $validated['consignment_mitra_id'] = null;
            }

            $product->update($validated);

            return redirect()->route('admin.inventory.index', ['tab' => 'products'])
                ->with('success', "Data produk \"{$product->name}\" berhasil diperbarui!");
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui produk: ' . $e->getMessage());
        }
    }

    public function destroyProduct(int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);
        $name = $product->name;
        $product->delete();

        return redirect()->route('admin.inventory.index', ['tab' => 'products'])
            ->with('success', "Produk \"{$name}\" berhasil dihapus.");
    }

    // ==========================================
    // ALAT RENTAL OUTDOOR
    // ==========================================

    public function storeRental(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'asset_code' => ['required', 'string', 'max:50', 'unique:rental_items,asset_code'],
            'name' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:50'],
            'rental_rate_per_day' => ['required', 'numeric', 'min:0'],
            'deposit_rate' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::enum(RentalAssetStatus::class)],
            'condition_notes' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $rental = RentalItem::create($validated);

            return redirect()->route('admin.inventory.index', ['tab' => 'rentals'])
                ->with('success', "Alat rental \"{$rental->name}\" ({$rental->asset_code}) berhasil didaftarkan!");
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal menambahkan alat rental: ' . $e->getMessage());
        }
    }

    public function updateRental(Request $request, int $id): RedirectResponse
    {
        $rental = RentalItem::findOrFail($id);

        $validated = $request->validate([
            'asset_code' => ['required', 'string', 'max:50', Rule::unique('rental_items')->ignore($rental->id)],
            'name' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:50'],
            'rental_rate_per_day' => ['required', 'numeric', 'min:0'],
            'deposit_rate' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::enum(RentalAssetStatus::class)],
            'condition_notes' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $rental->update($validated);

            return redirect()->route('admin.inventory.index', ['tab' => 'rentals'])
                ->with('success', "Data alat rental \"{$rental->name}\" berhasil diperbarui!");
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui alat rental: ' . $e->getMessage());
        }
    }

    public function destroyRental(int $id): RedirectResponse
    {
        $rental = RentalItem::findOrFail($id);
        $name = $rental->name;
        $rental->delete();

        return redirect()->route('admin.inventory.index', ['tab' => 'rentals'])
            ->with('success', "Alat rental \"{$name}\" berhasil dihapus.");
    }
}
