<?php

namespace App\Modules\Rental\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Rental\Models\RentalItem;
use App\Modules\Rental\Models\RentalOrder;
use App\Modules\Rental\Services\RentalService;
use App\Shared\Enums\RentalAssetStatus;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RentalController extends Controller
{
    public function __construct(
        protected RentalService $rentalService
    ) {}

    public function index(): View
    {
        $kanban = $this->rentalService->getKanbanAssets();
        $activeOrders = RentalOrder::with('items.item')
            ->where('status', 'ACTIVE')
            ->latest()
            ->get();

        return view('rental.index', compact('kanban', 'activeOrders'));
    }

    public function updateStatus(Request $request, int $assetId): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:TERSEDIA,DISEWA,PERLU_QC_CUCI,DALAM_PERBAIKAN',
            'notes' => 'nullable|string|max:255',
        ]);

        try {
            $status = RentalAssetStatus::from($validated['status']);
            $this->rentalService->updateAssetStatus($assetId, $status, $validated['notes'] ?? null);

            return back()->with('success', "Status aset berhasil diubah menjadi {$status->label()}.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function returnQc(Request $request, int $orderId): RedirectResponse
    {
        $validated = $request->validate([
            'inspections' => 'required|array',
            'inspections.*.condition' => 'required|in:BAIK,RUSAK_RINGAN,RUSAK_BERAT,HILANG',
            'inspections.*.penalty' => 'nullable|numeric|min:0',
            'inspections.*.notes' => 'nullable|string|max:255',
        ]);

        try {
            $order = $this->rentalService->processReturnQC($orderId, $validated['inspections']);

            $msg = "Pengembalian barang sewa #{$order->order_code} selesai di-QC. ";
            if ($order->damage_fee > 0) {
                $msg .= "Dikenakan denda kerusakan: Rp " . number_format($order->damage_fee, 0, ',', '.') . ". Sisa deposit dikembalikan: Rp " . number_format($order->net_refund_deposit, 0, ',', '.');
            } else {
                $msg .= "Seluruh barang dalam kondisi baik! Refund jaminan penuh: Rp " . number_format($order->total_deposit_amount, 0, ',', '.');
            }

            return back()->with('success', $msg);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
