<?php

namespace App\Modules\Ojek\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Ojek\Models\OjekQueueTicket;
use App\Modules\Ojek\Models\OjekRoute;
use App\Modules\Ojek\Services\OjekQueueService;
use App\Shared\Enums\OjekQueueStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicOjekController extends Controller
{
    public function __construct(
        protected OjekQueueService $queueService
    ) {}

    /**
     * Display Monitor TV Kiosk Pangkalan / Basecamp (Fullscreen TV Mode)
     */
    public function display(): View
    {
        $data = $this->queueService->getRealtimeDashboardData();
        return view('ojek.display', $data);
    }

    /**
     * Halaman Publik Pendaki: Cek Tarif, Rute & Status Antrean
     */
    public function publicView(): View
    {
        $routes = OjekRoute::where('is_active', true)->get();
        $todayTicketsCount = OjekQueueTicket::whereDate('created_at', today())->count();
        $waitingCount = OjekQueueTicket::whereDate('created_at', today())
            ->where('status', OjekQueueStatus::WAITING)
            ->count();

        return view('ojek.public', compact('routes', 'todayTicketsCount', 'waitingCount'));
    }

    /**
     * API Status untuk TV Kiosk Display
     */
    public function displayApi(): JsonResponse
    {
        return response()->json($this->queueService->getRealtimeDashboardData());
    }

    /**
     * Cek tiket pendaki mandiri berdasarkan nomor tiket
     */
    public function checkTicket(Request $request): JsonResponse
    {
        $code = trim((string) $request->input('code'));

        if (! $code) {
            return response()->json([
                'success' => false,
                'message' => 'Masukkan nomor tiket ojek Anda.',
            ], 422);
        }

        $ticket = OjekQueueTicket::with(['route', 'driver'])
            ->where('ticket_number', $code)
            ->first();

        if (! $ticket) {
            return response()->json([
                'success' => false,
                'message' => "Nomor tiket {$code} tidak ditemukan.",
            ], 404);
        }

        // Hitung perkiraan antrean di depannya jika masih WAITING
        $aheadCount = 0;
        if ($ticket->status === OjekQueueStatus::WAITING) {
            $aheadCount = OjekQueueTicket::whereDate('created_at', $ticket->created_at->toDateString())
                ->where('status', OjekQueueStatus::WAITING)
                ->where('id', '<', $ticket->id)
                ->count();
        }

        return response()->json([
            'success' => true,
            'ticket' => [
                'ticket_number' => $ticket->ticket_number,
                'passenger_name' => $ticket->passenger_name,
                'route_name' => $ticket->route->route_name,
                'fare_amount' => $ticket->fare_amount,
                'status' => $ticket->status->value,
                'status_label' => $ticket->status->label(),
                'status_badge' => $ticket->status->badgeClass(),
                'driver_name' => $ticket->driver?->name,
                'driver_plate' => $ticket->driver?->license_plate,
                'driver_motorcycle' => $ticket->driver?->motorcycle_model,
                'ahead_count' => $aheadCount,
                'called_at' => $ticket->called_at?->format('H:i'),
            ],
        ]);
    }
}
