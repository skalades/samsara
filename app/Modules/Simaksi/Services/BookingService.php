<?php

namespace App\Modules\Simaksi\Services;

use App\Modules\FieldOps\Models\EcoDeposit;
use App\Modules\Simaksi\Models\DailyQuota;
use App\Modules\Simaksi\Models\SimaksiBooking;
use App\Modules\Simaksi\Models\SimaksiMember;
use App\Modules\Simaksi\Models\TrailSetting;
use App\Shared\Enums\BookingStatus;
use App\Shared\Enums\PaymentMethod;
use App\Shared\Enums\TrailStatus;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class BookingService
{
    public function getOrCreateQuota(Carbon $date): DailyQuota
    {
        $dateStr = $date->toDateString();
        $isWeekend = $date->isWeekend();
        $defaultMax = $isWeekend ? 300 : 150;

        return DailyQuota::firstOrCreate(
            ['date' => $dateStr],
            [
                'max_quota' => $defaultMax,
                'booked_count' => 0,
                'is_blackout' => false,
            ]
        );
    }

    public function createBooking(array $data): SimaksiBooking
    {
        $trailSetting = TrailSetting::current();
        if ($trailSetting->trail_status === TrailStatus::CLOSED) {
            throw new Exception('Jalur pendakian saat ini sedang DITUTUP DARURAT oleh pengelola Basecamp SAMSARA.');
        }

        $hikingDate = Carbon::parse($data['hiking_date']);
        $totalHikers = count($data['members'] ?? []);
        if ($totalHikers < 1) {
            throw new Exception('Minimal 1 orang pendaki harus didaftarkan dalam rombongan.');
        }

        return DB::transaction(function () use ($hikingDate, $totalHikers, $data) {
            // Pessimistic Locking to ensure quota is never oversold
            $quota = DailyQuota::whereDate('date', $hikingDate->toDateString())
                ->lockForUpdate()
                ->first();

            if (! $quota) {
                $isWeekend = $hikingDate->isWeekend();
                $quota = DailyQuota::create([
                    'date' => $hikingDate->toDateString(),
                    'max_quota' => $isWeekend ? 300 : 150,
                    'booked_count' => 0,
                    'is_blackout' => false,
                ]);
            }

            if ($quota->is_blackout) {
                throw new Exception("Tanggal {$hikingDate->format('d M Y')} ditutup untuk pendakian (Blackout Date: " . ($quota->notes ?? 'Pembersihan Jalur') . ').');
            }

            if (($quota->booked_count + $totalHikers) > $quota->max_quota) {
                $remaining = max(0, $quota->max_quota - $quota->booked_count);
                throw new Exception("Sisa kuota untuk tanggal {$hikingDate->format('d M Y')} tidak mencukupi (Tersisa: {$remaining} orang).");
            }

            // Increment quota
            $quota->increment('booked_count', $totalHikers);

            $ticketFeePerPerson = (int) ($trailSetting->ticket_fee_per_person ?? 25000);
            $ecoDeposit = (int) ($trailSetting->eco_deposit_fee ?? 20000);
            $totalTicketAmount = $totalHikers * $ticketFeePerPerson;
            $bookingCode = 'SMX-' . $hikingDate->format('ymd') . '-' . strtoupper(Str::random(4));
            $qrToken = 'QR-' . Str::uuid()->toString();

            $booking = SimaksiBooking::create([
                'booking_code' => $bookingCode,
                'user_id' => $data['user_id'] ?? null,
                'hiking_date' => $hikingDate->toDateString(),
                'duration_days' => $data['duration_days'] ?? 2,
                'leader_name' => $data['leader_name'],
                'leader_nik' => $data['leader_nik'],
                'leader_phone' => $data['leader_phone'],
                'leader_email' => $data['leader_email'],
                'leader_address' => $data['leader_address'],
                'emergency_contact_name' => $data['emergency_contact_name'],
                'emergency_contact_phone' => $data['emergency_contact_phone'],
                'total_hikers' => $totalHikers,
                'ticket_fee_per_person' => $ticketFeePerPerson,
                'total_ticket_amount' => $totalTicketAmount,
                'eco_deposit_amount' => $ecoDeposit,
                'status' => BookingStatus::PENDING_PAYMENT,
                'payment_method' => PaymentMethod::from($data['payment_method'] ?? 'QRIS'),
                'current_pos' => 'Basecamp',
                'qr_token' => $qrToken,
                'tent_color_details' => $data['tent_color_details'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['members'] as $m) {
                SimaksiMember::create([
                    'booking_id' => $booking->id,
                    'full_name' => $m['full_name'],
                    'nik' => $m['nik'],
                    'phone' => $m['phone'] ?? null,
                    'gender' => $m['gender'] ?? 'L',
                    'age' => $m['age'] ?? 20,
                    'medical_history' => $m['medical_history'] ?? 'Tidak ada riwayat',
                    'emergency_contact_name' => $m['emergency_contact_name'] ?? $data['emergency_contact_name'],
                    'emergency_contact_phone' => $m['emergency_contact_phone'] ?? $data['emergency_contact_phone'],
                ]);
            }

            // Create Eco-deposit track record
            EcoDeposit::create([
                'booking_id' => $booking->id,
                'deposit_amount' => $ecoDeposit,
                'brought_plastics_count' => $data['brought_plastics_count'] ?? ($totalHikers * 2),
                'brought_cans_count' => $data['brought_cans_count'] ?? 1,
                'brought_bottles_count' => $data['brought_bottles_count'] ?? ($totalHikers * 2),
                'deposit_status' => 'HELD',
            ]);

            return $booking->fresh(['members', 'ecoDeposit']);
        });
    }

    /**
     * Konfirmasi pembayaran oleh admin: set status CONFIRMED dan catat paid_at.
     */
    public function confirmPayment(int $bookingId, int $adminUserId): SimaksiBooking
    {
        return DB::transaction(function () use ($bookingId, $adminUserId) {
            $booking = SimaksiBooking::lockForUpdate()->findOrFail($bookingId);

            if ($booking->status !== BookingStatus::PENDING_PAYMENT) {
                throw new RuntimeException("Booking {$booking->booking_code} tidak dalam status menunggu pembayaran.");
            }

            $booking->update([
                'status'  => BookingStatus::CONFIRMED,
                'paid_at' => now(),
            ]);

            return $booking->fresh();
        });
    }

    /**
     * Tolak / batalkan booking oleh admin: set status CANCELLED dan kembalikan kuota harian.
     */
    public function rejectBooking(int $bookingId, int $adminUserId, string $reason): SimaksiBooking
    {
        return DB::transaction(function () use ($bookingId, $adminUserId, $reason) {
            $booking = SimaksiBooking::lockForUpdate()->findOrFail($bookingId);

            if ($booking->status !== BookingStatus::PENDING_PAYMENT) {
                throw new RuntimeException("Booking {$booking->booking_code} tidak dalam status menunggu pembayaran.");
            }

            // Kembalikan kuota ke DailyQuota
            $quota = DailyQuota::whereDate('date', $booking->hiking_date->toDateString())
                ->lockForUpdate()
                ->first();

            if ($quota) {
                $quota->decrement('booked_count', $booking->total_hikers);
            }

            $booking->update([
                'status' => BookingStatus::CANCELLED,
                'notes'  => ($booking->notes ? $booking->notes . "\n" : '') . "Dibatalkan admin: {$reason}",
            ]);

            return $booking->fresh();
        });
    }
}
