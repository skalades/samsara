<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Services\AdminService;
use App\Modules\Admin\Services\AnalyticsService;
use App\Modules\Simaksi\Models\DailyQuota;
use App\Modules\Simaksi\Models\SimaksiBooking;
use App\Modules\Simaksi\Models\SosAlert;
use App\Modules\Simaksi\Models\TrailSetting;
use App\Modules\Simaksi\Services\BookingService;
use App\Shared\Enums\BookingStatus;
use App\Shared\Enums\TrailStatus;
use App\Shared\Enums\WeatherStatus;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function __construct(
        protected AdminService $adminService,
        protected BookingService $bookingService,
        protected AnalyticsService $analyticsService,
    ) {}

    public function analytics(Request $request): View
    {
        $year = (int) $request->input('year', date('Y'));
        $monthlyTrends = $this->analyticsService->getMonthlyHikerTrends($year);
        $peakHeatmap = $this->analyticsService->getPeakDaysHeatmap($year);
        $conversionStats = $this->analyticsService->getConversionRateStats();
        $topProducts = $this->analyticsService->getTopSellingProducts(8);

        return view('admin.analytics', compact(
            'year',
            'monthlyTrends',
            'peakHeatmap',
            'conversionStats',
            'topProducts'
        ));
    }

    public function index(): View
    {
        $pulse = $this->adminService->getMountainPulse();
        $posDistribution = $this->adminService->getLivePosTracker();
        $recentBookings = SimaksiBooking::with('members')->latest()->take(8)->get();
        $activeSosList = SosAlert::where('status', 'ACTIVE')->latest()->get();
        $pendingBookingsCount = $pulse['pending_payments_count'];

        return view('admin.index', compact('pulse', 'posDistribution', 'recentBookings', 'activeSosList', 'pendingBookingsCount'));
    }

    public function emergencyTrailControl(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'trail_status' => 'required|in:OPEN,WARNING,CLOSED',
            'weather_status' => 'required|in:CERAH,BERAWAN,HUJAN_RINGAN,HUJAN_LEBAT,BADAI',
            'announcement' => 'nullable|string|max:500',
        ]);

        try {
            $status = TrailStatus::from($validated['trail_status']);
            $weather = WeatherStatus::from($validated['weather_status']);
            $this->adminService->updateTrailControl(
                $status,
                $weather,
                $validated['announcement'] ?? null,
                auth()->id()
            );

            return back()->with('success', "Status Jalur Operasional berhasil diperbarui: {$status->label()} - Cuaca: {$weather->label()}");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function posTracker(): View
    {
        $posDistribution = $this->adminService->getLivePosTracker();
        $trailSetting = TrailSetting::current();

        return view('admin.tracker', compact('posDistribution', 'trailSetting'));
    }

    public function sarExport(int $bookingId): View
    {
        $booking = $this->adminService->getSarManifestData($bookingId);
        $trailSetting = TrailSetting::current();

        return view('admin.sar-export', compact('booking', 'trailSetting'));
    }

    public function financialReport(Request $request): View
    {
        $startStr = $request->input('start_date', today()->startOfMonth()->toDateString());
        $endStr = $request->input('end_date', today()->toDateString());

        $report = $this->adminService->getFinancialReport(
            Carbon::parse($startStr),
            Carbon::parse($endStr)
        );

        return view('admin.finance', compact('report', 'startStr', 'endStr'));
    }

    public function quotaManager(Request $request): View
    {
        $quotas = DailyQuota::where('date', '>=', today()->toDateString())
            ->orderBy('date')
            ->take(30)
            ->get();

        return view('admin.quota', compact('quotas'));
    }

    public function updateQuota(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'max_quota' => 'required|integer|min:0|max:1000',
            'is_blackout' => 'nullable|boolean',
            'notes' => 'nullable|string|max:255',
        ]);

        DailyQuota::updateOrCreate(
            ['date' => $validated['date']],
            [
                'max_quota' => $validated['max_quota'],
                'is_blackout' => (bool) ($validated['is_blackout'] ?? false),
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return back()->with('success', "Kuota tanggal {$validated['date']} berhasil diperbarui!");
    }

    // ======================================================
    // Manajemen Konfirmasi Pembayaran Manual (Admin)
    // ======================================================

    /**
     * Daftar booking yang menunggu konfirmasi pembayaran.
     */
    public function pendingBookings(Request $request): View
    {
        $search = $request->input('search', '');

        $bookings = SimaksiBooking::with('members')
            ->where('status', BookingStatus::PENDING_PAYMENT)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('booking_code', 'like', "%{$search}%")
                      ->orWhere('leader_name', 'like', "%{$search}%")
                      ->orWhere('leader_phone', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.bookings-pending', compact('bookings', 'search'));
    }

    /**
     * Registrasi langsung SIMAKSI Walk-in / On-The-Spot oleh Admin Basecamp.
     */
    public function storeWalkInBooking(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'hiking_date' => 'required|date|after_or_equal:today',
            'duration_days' => 'required|integer|min:1|max:4',
            'leader_name' => 'required|string|max:150',
            'leader_nik' => 'required|digits:16',
            'leader_phone' => 'required|string|max:20',
            'leader_email' => 'nullable|email|max:100',
            'leader_address' => 'nullable|string|max:255',
            'emergency_contact_name' => 'required|string|max:150',
            'emergency_contact_phone' => 'required|string|max:20',
            'payment_status' => 'required|in:PENDING_PAYMENT,CONFIRMED',
            'tent_color_details' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
            'members' => 'required|array|min:1',
            'members.*.full_name' => 'required|string|max:150',
            'members.*.nik' => 'nullable|digits:16',
            'members.*.gender' => 'required|in:L,P',
            'members.*.age' => 'required|integer|min:7|max:80',
            'members.*.medical_history' => 'nullable|string|max:255',
        ]);

        try {
            $validated['leader_email'] = $validated['leader_email'] ?? ('walkin_' . time() . '@samsara.basecamp');
            $validated['leader_address'] = $validated['leader_address'] ?? 'Walk-in Basecamp Cigedug';
            $validated['payment_method'] = 'CASH';
            $validated['notes'] = trim(($validated['notes'] ?? '') . ' [Registrasi Staf/Admin Basecamp]');

            foreach ($validated['members'] as &$member) {
                if (empty($member['nik'])) {
                    $member['nik'] = $validated['leader_nik'];
                }
                if (empty($member['medical_history'])) {
                    $member['medical_history'] = 'Sehat / Tidak ada riwayat';
                }
            }

            $booking = $this->bookingService->createBooking($validated);

            if ($request->input('payment_status') === 'CONFIRMED') {
                $this->bookingService->confirmPayment($booking->id, auth()->id());
                $statusMsg = "dan pembayaran telah dikonfirmasi (Lunas)";
            } else {
                $statusMsg = "dengan status Menunggu Pembayaran di Loket";
            }

            return redirect()->route('admin.bookings.pending')
                ->with('success', "Registrasi walk-in rombongan {$booking->leader_name} ({$booking->booking_code}) berhasil dibuat {$statusMsg}!");
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Konfirmasi pembayaran booking oleh admin.
     */
    public function confirmBookingPayment(Request $request, int $bookingId): RedirectResponse
    {
        try {
            $booking = $this->bookingService->confirmPayment($bookingId, auth()->id());

            return redirect()->route('admin.bookings.pending')
                ->with('success', "Pembayaran booking {$booking->booking_code} atas nama {$booking->leader_name} berhasil dikonfirmasi!");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Tolak / batalkan booking oleh admin.
     */
    public function rejectBookingPayment(Request $request, int $bookingId): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        try {
            $booking = $this->bookingService->rejectBooking($bookingId, auth()->id(), $validated['reason']);

            return redirect()->route('admin.bookings.pending')
                ->with('success', "Booking {$booking->booking_code} berhasil dibatalkan. Kuota harian telah dikembalikan.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}

