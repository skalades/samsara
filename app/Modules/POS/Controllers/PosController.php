<?php

namespace App\Modules\POS\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\POS\Models\CashierShift;
use App\Modules\POS\Models\PosOrder;
use App\Modules\POS\Services\PosService;
use App\Modules\Rental\Models\RentalItem;
use App\Modules\Simaksi\Models\SimaksiBooking;
use App\Modules\Store\Models\Product;
use App\Shared\Enums\BookingStatus;
use App\Shared\Enums\RentalAssetStatus;
use App\Shared\Enums\UserRole;
use App\Shared\Helpers\CurrencyHelper;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PosController extends Controller
{
    public function __construct(
        protected PosService $posService
    ) {}

    public function index(Request $request): View
    {
        $cashierId = auth()->id();
        $activeShift = $this->posService->getActiveShift($cashierId);
        $user = auth()->user();
        $isKasir = $user && $user->role === UserRole::KASIR;

        $products = Product::with('mitra')
            ->where('active', true)
            ->where('stock', '>', 0)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $availableRentals = RentalItem::where('status', RentalAssetStatus::TERSEDIA)
            ->orderBy('name')
            ->get();

        $recentOrders = PosOrder::with('items')
            ->latest()
            ->take(8)
            ->get();

        $ojekRoutes = \App\Modules\Ojek\Models\OjekRoute::where('is_active', true)->get();

        return view('pos.index', compact('activeShift', 'products', 'availableRentals', 'recentOrders', 'isKasir', 'ojekRoutes'));
    }

    public function checkout(Request $request): JsonResponse
    {
        if ($request->has('paid_amount')) {
            $request->merge(['paid_amount' => CurrencyHelper::parse($request->input('paid_amount'))]);
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:150',
            'customer_phone' => 'nullable|string|max:20',
            'booking_id' => 'nullable|exists:simaksi_bookings,id',
            'payment_method' => 'required|in:CASH,QRIS,TRANSFER',
            'paid_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:255',
            'ojek_route_id' => 'nullable|exists:ojek_routes,id',
            'ojek_qty' => 'nullable|integer|min:1',
            'items' => 'nullable|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'rentals' => 'nullable|array',
            'rentals.*.rental_item_id' => 'required|exists:rental_items,id',
            'rentals.*.rental_days' => 'required|integer|min:1',
        ]);

        try {
            $cashierId = auth()->id();
            $user = auth()->user();

            // Level Kasir tidak bisa melakukan transaksi POS sebelum buka shift
            if ($user && $user->role === UserRole::KASIR) {
                $activeShift = $this->posService->getActiveShift($cashierId);
                if (! $activeShift) {
                    return response()->json([
                        'success' => false,
                        'requires_shift' => true,
                        'message' => 'Kasir belum membuka shift. Silakan buka shift kasir terlebih dahulu untuk memulai transaksi POS.',
                    ], 422);
                }
            }

            $order = $this->posService->processCheckout($validated, $cashierId);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi kasir berhasil disimpan.',
                'order' => $order,
                'invoice_url' => route('pos.invoice', $order->invoice_number),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function invoice(string $invoiceNumber): View
    {
        $order = PosOrder::with(['items.mitra', 'booking.members', 'cashier'])
            ->where('invoice_number', $invoiceNumber)
            ->firstOrFail();

        return view('pos.invoice', compact('order'));
    }

    public function pickupScan(Request $request): JsonResponse
    {
        $code = trim($request->input('code', ''));
        if (! $code) {
            return response()->json(['success' => false, 'message' => 'Kode booking atau barcode pre-order wajib diisi.'], 400);
        }

        try {
            $result = $this->posService->markPreorderPickup($code);

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'booking' => $result['booking'] ?? null,
                'order' => $result['order'] ?? null,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Data tidak ditemukan: {$e->getMessage()}",
            ], 404);
        }
    }

    public function shiftReconciliation(Request $request): View
    {
        $cashierId = auth()->id();
        $activeShift = $this->posService->getActiveShift($cashierId);
        $recentShifts = CashierShift::with('cashier')->latest()->take(10)->get();

        return view('pos.shift', compact('activeShift', 'recentShifts'));
    }

    public function openShift(Request $request): RedirectResponse|JsonResponse
    {
        $cashierId = auth()->id();
        $openingCash = CurrencyHelper::parse($request->input('opening_cash', 0));
        $shift = $this->posService->openShift($cashierId, $openingCash);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Shift kasir berhasil DIBUKA dengan modal awal Rp ' . number_format($openingCash, 0, ',', '.'),
                'shift' => $shift,
            ]);
        }

        return back()->with('success', 'Shift kasir berhasil DIBUKA dengan modal awal Rp ' . number_format($openingCash, 0, ',', '.'));
    }

    public function closeShift(Request $request, int $shiftId): RedirectResponse
    {
        $physicalCash = CurrencyHelper::parse($request->input('closing_cash_physical', 0));
        $notes = $request->input('notes');

        $shift = $this->posService->closeShift($shiftId, $physicalCash, $notes);

        $diffText = $shift->cash_difference == 0
            ? 'Tepat (Pas)'
            : ($shift->cash_difference > 0 ? '+Rp ' . number_format($shift->cash_difference, 0, ',', '.') : '-Rp ' . number_format(abs($shift->cash_difference), 0, ',', '.'));

        return back()->with('success', "Shift kasir berhasil DITUTUP. Selisih kas fisik: {$diffText}");
    }
}
