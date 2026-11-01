<?php

namespace App\Modules\Ojek\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Ojek\Models\OjekDriver;
use App\Modules\Ojek\Models\OjekDriverAttendance;
use App\Modules\Ojek\Models\OjekRoute;
use App\Modules\Ojek\Services\OjekQueueService;
use App\Shared\Enums\OjekDriverStatus;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OjekQueueController extends Controller
{
    public function __construct(
        protected OjekQueueService $queueService
    ) {}

    /**
     * Dashboard Koordinator Pangkalan Ojek (Mobile-First Thumb-Zone)
     */
    public function index(): View
    {
        $data = $this->queueService->getRealtimeDashboardData();
        $allDrivers = OjekDriver::where('is_active', true)->orderBy('name')->get();
        $routes = OjekRoute::where('is_active', true)->get();

        return view('ojek.index', array_merge($data, [
            'all_drivers' => $allDrivers,
            'available_routes' => $routes,
        ]));
    }

    /**
     * Panggil giliran berikutnya (Memasangkan antrean FIFO)
     */
    public function callNext(Request $request): JsonResponse
    {
        try {
            $ticketId = $request->input('ticket_id');
            $attendanceId = $request->input('attendance_id');

            $ticket = $this->queueService->callNextQueue($ticketId, $attendanceId);

            return response()->json([
                'success' => true,
                'message' => "Giliran nomor {$ticket->ticket_number} berhasil dipanggil!",
                'ticket' => $ticket,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Mulai perjalanan (Driver & Penumpang berangkat)
     */
    public function startTrip(int $id): JsonResponse
    {
        try {
            $ticket = $this->queueService->startTrip($id);

            return response()->json([
                'success' => true,
                'message' => "Perjalanan tiket {$ticket->ticket_number} dimulai.",
                'ticket' => $ticket,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Selesai mengantar (Driver kembali dan masuk antrean ekor)
     */
    public function completeTrip(int $id): JsonResponse
    {
        try {
            $ticket = $this->queueService->completeTrip($id);

            return response()->json([
                'success' => true,
                'message' => "Trip tiket {$ticket->ticket_number} selesai. Driver kembali ke antrean.",
                'ticket' => $ticket,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Batalkan tiket antrean
     */
    public function cancelTicket(int $id, Request $request): JsonResponse
    {
        try {
            $ticket = $this->queueService->cancelTicket($id, $request->input('reason'));

            return response()->json([
                'success' => true,
                'message' => "Tiket {$ticket->ticket_number} telah dibatalkan.",
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Check-in driver hadir hari ini
     */
    public function checkInDriver(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'driver_id' => 'required|exists:ojek_drivers,id',
        ]);

        try {
            $attendance = $this->queueService->checkInDriver((int) $validated['driver_id']);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Driver {$attendance->driver->name} berhasil masuk antrean giliran.",
                    'attendance' => $attendance,
                ]);
            }

            return back()->with('success', "Driver {$attendance->driver->name} berhasil masuk antrean giliran.");
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Ubah status driver (Break, Standby, Offline)
     */
    public function updateDriverStatus(int $id, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:STANDBY,BREAK,OFFLINE',
        ]);

        try {
            $status = OjekDriverStatus::from($validated['status']);
            $attendance = $this->queueService->updateDriverStatus($id, $status);

            return response()->json([
                'success' => true,
                'message' => "Status driver {$attendance->driver->name} diubah menjadi {$status->label()}.",
                'attendance' => $attendance,
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Buat tiket walk-in langsung dari pangkalan
     */
    public function storeWalkIn(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ojek_route_id' => 'required|exists:ojek_routes,id',
            'passenger_name' => 'required|string|max:100',
            'passenger_phone' => 'nullable|string|max:20',
            'payment_method' => 'required|in:CASH,QRIS,TRANSFER',
            'notes' => 'nullable|string|max:255',
        ]);

        try {
            $validated['source'] = 'PANGKALAN_WALKIN';
            $validated['payment_status'] = 'PAID';

            $ticket = $this->queueService->createTicket($validated);

            return response()->json([
                'success' => true,
                'message' => "Tiket nomor {$ticket->ticket_number} berhasil dibuat.",
                'ticket' => $ticket,
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Real-time JSON data endpoint
     */
    public function realtimeApi(): JsonResponse
    {
        return response()->json($this->queueService->getRealtimeDashboardData());
    }

    /**
     * Halaman Manajemen Anggota Driver Paguyuban
     */
    public function drivers(): View
    {
        $drivers = OjekDriver::with('todayAttendance')->latest()->get();
        return view('ojek.drivers', compact('drivers'));
    }

    /**
     * Tambah Anggota Driver Baru
     */
    public function storeDriver(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'phone' => 'required|string|max:20',
            'license_plate' => 'required|string|max:20',
            'motorcycle_model' => 'nullable|string|max:100',
            'community_name' => 'nullable|string|max:150',
        ]);

        $driver = DB::transaction(function () use ($validated) {
            $latest = OjekDriver::lockForUpdate()->orderByDesc('id')->first();
            $nextNum = 1;
            if ($latest && preg_match('/(\d+)$/', $latest->driver_number, $matches)) {
                $nextNum = ((int) $matches[1]) + 1;
            }

            do {
                $driverNumber = sprintf('DRV-%03d', $nextNum++);
            } while (OjekDriver::where('driver_number', $driverNumber)->exists());

            return OjekDriver::create(array_merge($validated, [
                'driver_number' => $driverNumber,
                'is_active' => true,
            ]));
        });

        return back()->with('success', "Driver baru {$validated['name']} ({$driver->driver_number}) berhasil didaftarkan.");
    }

    /**
     * Toggle status aktif driver
     */
    public function toggleDriverActive(int $id): RedirectResponse
    {
        $driver = OjekDriver::findOrFail($id);
        $driver->update(['is_active' => ! $driver->is_active]);

        $statusStr = $driver->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Driver {$driver->name} telah {$statusStr}.");
    }
}
