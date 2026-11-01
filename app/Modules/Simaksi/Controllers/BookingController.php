<?php

namespace App\Modules\Simaksi\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Simaksi\Models\SimaksiBooking;
use App\Modules\Simaksi\Models\TrailSetting;
use App\Modules\Simaksi\Services\BookingService;
use App\Modules\Simaksi\Services\QuotaService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected QuotaService $quotaService
    ) {}

    public function index(Request $request): View
    {
        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);
        $calendar = $this->quotaService->getMonthlyCalendar($year, $month);
        $trailSetting = TrailSetting::current();

        return view('simaksi.index', compact('calendar', 'year', 'month', 'trailSetting'));
    }

    public function create(Request $request): View
    {
        $selectedDate = $request->input('date', today()->addDay()->toDateString());
        $trailSetting = TrailSetting::current();

        return view('simaksi.create', compact('selectedDate', 'trailSetting'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'hiking_date' => 'required|date|after_or_equal:today',
            'duration_days' => 'required|integer|min:1|max:4',
            'leader_name' => 'required|string|max:150',
            'leader_nik' => 'required|digits:16',
            'leader_phone' => 'required|string|max:20',
            'leader_email' => 'required|email|max:100',
            'leader_address' => 'required|string|max:255',
            'emergency_contact_name' => 'required|string|max:150',
            'emergency_contact_phone' => 'required|string|max:20',
            'payment_method' => 'required|in:QRIS,CASH,TRANSFER',
            'tent_color_details' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
            'members' => 'required|array|min:1',
            'members.*.full_name' => 'required|string|max:150',
            'members.*.nik' => 'required|digits:16',
            'members.*.gender' => 'required|in:L,P',
            'members.*.age' => 'required|integer|min:10|max:75',
            'members.*.phone' => 'nullable|string|max:20',
            'members.*.medical_history' => 'nullable|string|max:255',
        ]);

        try {
            $booking = $this->bookingService->createBooking($validated);

            return redirect()->route('simaksi.show', $booking->booking_code)
                ->with('success', 'Reservasi SIMAKSI Cikuray berhasil dibuat! Silakan simpan E-Ticket Anda.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(string $bookingCode): View
    {
        $booking = SimaksiBooking::with(['members', 'checkpoints', 'ecoDeposit'])
            ->where('booking_code', $bookingCode)
            ->orWhere('qr_token', $bookingCode)
            ->firstOrFail();

        $trailSetting = TrailSetting::current();

        return view('simaksi.show', compact('booking', 'trailSetting'));
    }

    public function uploadPaymentProof(Request $request, string $bookingCode): RedirectResponse
    {
        $booking = SimaksiBooking::where('booking_code', $bookingCode)
            ->orWhere('qr_token', $bookingCode)
            ->firstOrFail();

        if ($booking->status !== \App\Shared\Enums\BookingStatus::PENDING_PAYMENT) {
            return back()->with('error', 'Status booking ini tidak dalam status menunggu pembayaran.');
        }

        $request->validate([
            'payment_proof' => 'required|file|mimes:jpeg,png,jpg,webp,pdf|max:3072',
        ], [
            'payment_proof.required' => 'Silakan pilih file foto atau struk bukti transfer.',
            'payment_proof.mimes' => 'Format file bukti transfer harus berupa JPG, PNG, WEBP, atau PDF.',
            'payment_proof.max' => 'Ukuran file maksimal 3 MB.',
        ]);

        $file = $request->file('payment_proof');
        $fileName = 'proof_' . Str::slug($booking->booking_code) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $storedPath = \Illuminate\Support\Facades\Storage::disk('public')->putFileAs('proofs', $file, $fileName);

        $booking->update([
            'payment_proof_path' => 'storage/' . $storedPath,
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diunggah! Petugas Basecamp SAMSARA akan segera memverifikasi pesanan Anda.');
    }

    public function checkQuota(Request $request): JsonResponse
    {
        $dateStr = $request->input('date', today()->toDateString());
        $date = Carbon::parse($dateStr);
        $quota = $this->bookingService->getOrCreateQuota($date);

        return response()->json([
            'date' => $quota->date->toDateString(),
            'max_quota' => $quota->max_quota,
            'booked_count' => $quota->booked_count,
            'remaining_quota' => $quota->remaining_quota,
            'percentage_used' => $quota->percentage_used,
            'is_blackout' => $quota->is_blackout,
            'notes' => $quota->notes,
        ]);
    }
}
