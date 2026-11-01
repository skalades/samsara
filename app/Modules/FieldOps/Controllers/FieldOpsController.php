<?php

namespace App\Modules\FieldOps\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\FieldOps\Services\FieldOpsService;
use App\Modules\Simaksi\Models\SimaksiBooking;
use App\Modules\Simaksi\Models\SosAlert;
use App\Modules\Simaksi\Models\TrailCheckpoint;
use App\Modules\Simaksi\Models\TrailSetting;
use App\Shared\Enums\BookingStatus;
use App\Shared\Enums\SosType;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FieldOpsController extends Controller
{
    public function __construct(
        protected FieldOpsService $fieldOpsService
    ) {}

    public function index(Request $request): View
    {
        $staffPos = auth()->user()?->assigned_pos ?? $request->input('pos', 'Pos 1 (Tanjakan EOD)');
        $trailSetting = TrailSetting::current();

        $recentCheckpoints = TrailCheckpoint::with(['booking', 'scanner'])
            ->latest()
            ->take(12)
            ->get();

        $activeSos = SosAlert::where('status', 'ACTIVE')->latest()->get();

        $activeOnTrail = SimaksiBooking::whereIn('status', [BookingStatus::CHECKED_IN, BookingStatus::ON_TRAIL])
            ->latest()
            ->take(10)
            ->get();

        return view('fieldops.index', compact('staffPos', 'trailSetting', 'recentCheckpoints', 'activeSos', 'activeOnTrail'));
    }

    public function scan(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'qr_data' => 'required|string',
            'pos_name' => 'required|string',
            'direction' => 'required|in:ASCENDING,DESCENDING',
            'notes' => 'nullable|string|max:255',
        ]);

        try {
            $staffId = auth()->id();
            if (!$staffId) {
                return response()->json(['success' => false, 'message' => 'Sesi petugas tidak valid. Silakan login kembali.'], 401);
            }

            $result = $this->fieldOpsService->scanCheckpoint(
                trim($validated['qr_data']),
                $validated['pos_name'],
                $validated['direction'],
                $validated['notes'] ?? null,
                $staffId
            );

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function syncOffline(Request $request): JsonResponse
    {
        $queue = $request->input('queue', []);
        $results = [];
        $staffId = auth()->id();
        if (!$staffId) {
            return response()->json(['success' => false, 'message' => 'Sesi petugas tidak valid.'], 401);
        }

        foreach ($queue as $item) {
            try {
                $res = $this->fieldOpsService->scanCheckpoint(
                    $item['qr_data'],
                    $item['pos_name'],
                    $item['direction'] ?? 'ASCENDING',
                    ($item['notes'] ?? '') . ' [Offline Synced]',
                    $staffId
                );
                $results[] = ['qr' => $item['qr_data'], 'status' => 'OK', 'msg' => $res['message']];
            } catch (Exception $e) {
                $results[] = ['qr' => $item['qr_data'], 'status' => 'FAIL', 'msg' => $e->getMessage()];
            }
        }

        return response()->json([
            'success' => true,
            'synced_count' => count($results),
            'results' => $results,
        ]);
    }

    public function sosAlert(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'booking_id' => 'nullable|exists:simaksi_bookings,id',
            'reporter_name' => 'required|string|max:150',
            'reporter_phone' => 'required|string|max:20',
            'pos_location' => 'required|string|max:100',
            'alert_type' => 'required|in:MEDICAL,LOST,HYPOTHERMIA,WEATHER_TRAPPED,ACCIDENT',
            'description' => 'required|string|max:500',
        ]);

        try {
            $sos = $this->fieldOpsService->createSosAlert($validated);

            return response()->json([
                'success' => true,
                'message' => 'Peringatan Darurat SOS telah disebarkan ke SAR Console Basecamp SAMSARA!',
                'sos' => $sos,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function verifyWaste(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:simaksi_bookings,id',
            'returned_plastics_count' => 'required|integer|min:0',
            'returned_cans_count' => 'required|integer|min:0',
            'returned_bottles_count' => 'required|integer|min:0',
        ]);

        try {
            $staffId = auth()->id();
            if (!$staffId) {
                return response()->json(['success' => false, 'message' => 'Sesi petugas tidak valid.'], 401);
            }

            $deposit = $this->fieldOpsService->verifyEcoDeposit($validated['booking_id'], $validated, $staffId);

            $statusText = $deposit->deposit_status === 'REFUNDED'
                ? 'Lolos Pemeriksaan Sampah! Uang Jaminan Rp ' . number_format($deposit->deposit_amount, 0, ',', '.') . ' dikembalikan ke pendaki.'
                : 'Pemeriksaan Sampah Kurang! Uang jaminan hangus karena sampah tertinggal di gunung.';

            return response()->json([
                'success' => true,
                'deposit_status' => $deposit->deposit_status,
                'message' => $statusText,
                'deposit' => $deposit,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
