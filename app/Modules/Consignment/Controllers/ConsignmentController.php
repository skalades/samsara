<?php

namespace App\Modules\Consignment\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Consignment\Models\ConsignmentMitra;
use App\Modules\Consignment\Models\ConsignmentSettlement;
use App\Modules\Consignment\Services\ConsignmentService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsignmentController extends Controller
{
    public function __construct(
        protected ConsignmentService $consignmentService
    ) {}

    public function index(): View
    {
        $mitras = ConsignmentMitra::with(['products', 'settlements'])->get();
        $recentSettlements = ConsignmentSettlement::with('mitra')->latest()->take(10)->get();

        return view('consignment.index', compact('mitras', 'recentSettlements'));
    }

    public function generateSettlement(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mitra_id' => 'required|exists:consignment_mitras,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'notes' => 'nullable|string|max:255',
        ]);

        try {
            $settlement = $this->consignmentService->generateSettlementForMitra(
                (int) $validated['mitra_id'],
                Carbon::parse($validated['period_start']),
                Carbon::parse($validated['period_end']),
                $validated['notes'] ?? null
            );

            if (! $settlement) {
                return back()->with('warning', 'Tidak ada penjualan produk pada periode tanggal tersebut untuk mitra ini.');
            }

            return back()->with('success', "Settlement berhasil dibuat dengan kode {$settlement->settlement_code}. Slip WA siap dikirim!");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function showSettlement(int $id): View
    {
        $settlement = ConsignmentSettlement::with('mitra')->findOrFail($id);
        $whatsappText = $this->consignmentService->generateWhatsAppSlip($settlement);

        return view('consignment.show', compact('settlement', 'whatsappText'));
    }

    public function confirmSettlement(int $id): RedirectResponse
    {
        try {
            $settlement = $this->consignmentService->confirmSettlement($id);

            return redirect()->route('consignment.settlement.show', $settlement->id)
                ->with('success', "Settlement {$settlement->settlement_code} untuk {$settlement->mitra->name} berhasil dikonfirmasi LUNAS DITRANSFER!");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
