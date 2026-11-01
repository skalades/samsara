<?php

namespace App\Modules\Store\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Consignment\Models\ConsignmentMitra;
use App\Modules\POS\Services\PosService;
use App\Modules\Store\Services\StoreService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreController extends Controller
{
    public function __construct(
        protected StoreService $storeService,
        protected PosService $posService
    ) {}

    public function index(Request $request): View
    {
        $category = $request->input('category', 'ALL');
        $inventoryType = $request->input('type', 'ALL');
        $products = $this->storeService->getActiveCatalog($category, $inventoryType);
        $mitras = ConsignmentMitra::where('active', true)->get();

        return view('store.index', compact('products', 'category', 'inventoryType', 'mitras'));
    }

    public function preorder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:150',
            'customer_phone' => 'required|string|max:20',
            'booking_code' => 'nullable|string|max:50',
            'payment_method' => 'required|in:QRIS,CASH,TRANSFER',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        try {
            $validated['is_preorder_pickup'] = true;

            if (!empty($validated['booking_code'])) {
                $booking = \App\Modules\Simaksi\Models\SimaksiBooking::where('booking_code', trim($validated['booking_code']))
                    ->orWhere('qr_token', trim($validated['booking_code']))
                    ->first();

                if ($booking) {
                    $validated['booking_id'] = $booking->id;
                }
            }

            $order = $this->posService->processCheckout($validated);

            return redirect()->route('store.success', ['invoice' => $order->invoice_number])
                ->with('success', 'Pre-Order berhasil diproses! Tunjukkan barcode ini saat pengambilan di kasir basecamp.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function success(string $invoice): View
    {
        $order = \App\Modules\POS\Models\PosOrder::with('items.mitra')
            ->where('invoice_number', $invoice)
            ->firstOrFail();

        return view('store.success', compact('order'));
    }
}
