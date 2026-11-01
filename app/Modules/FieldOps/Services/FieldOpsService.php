<?php

namespace App\Modules\FieldOps\Services;

use App\Modules\FieldOps\Models\EcoDeposit;
use App\Modules\Simaksi\Models\SimaksiBooking;
use App\Modules\Simaksi\Models\SosAlert;
use App\Modules\Simaksi\Models\TrailCheckpoint;
use App\Shared\Enums\BookingStatus;
use App\Shared\Enums\SosType;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FieldOpsService
{
    public function scanCheckpoint(string $qrTokenOrCode, string $posName, string $direction = 'ASCENDING', ?string $notes = null, ?int $staffUserId = null): array
    {
        return DB::transaction(function () use ($qrTokenOrCode, $posName, $direction, $notes, $staffUserId) {
            $booking = SimaksiBooking::where('qr_token', $qrTokenOrCode)
                ->orWhere('booking_code', $qrTokenOrCode)
                ->first();

            if (! $booking) {
                throw new Exception("Data booking/tiket untuk barcode '{$qrTokenOrCode}' tidak ditemukan!");
            }

            // Validasi status tiket: cegah tiket belum lunas atau dibatalkan masuk jalur
            if ($booking->status === BookingStatus::PENDING_PAYMENT) {
                throw new Exception("Tiket {$booking->booking_code} BELUM LUNAS! Arahkan rombongan untuk menyelesaikan pembayaran di loket Basecamp terlebih dahulu.");
            }

            if ($booking->status === BookingStatus::CANCELLED) {
                throw new Exception("Tiket {$booking->booking_code} telah DIBATALKAN. Rombongan tidak diizinkan masuk jalur pendakian!");
            }

            // Cek apakah scan ini sudah pernah diproses baru-baru ini (deduplication untuk double-scan / offline sync)
            $existingCheckpoint = TrailCheckpoint::where('booking_id', $booking->id)
                ->where('pos_name', $posName)
                ->where('direction', $direction)
                ->where('created_at', '>=', now()->subMinutes(5))
                ->first();

            if ($existingCheckpoint) {
                $booking->current_pos = $posName;
                $booking->save();

                return [
                    'success'      => true,
                    'booking'      => $booking->load(['members', 'ecoDeposit']),
                    'checkpoint'   => $existingCheckpoint,
                    'is_duplicate' => true,
                    'message'      => "Scan duplikat diabaikan: {$booking->leader_name} ({$booking->total_hikers} orang) sudah tercatat di {$posName} arah {$direction}.",
                ];
            }

            // Record checkpoint log
            $checkpoint = TrailCheckpoint::create([
                'booking_id' => $booking->id,
                'pos_name' => $posName,
                'scanned_by_user_id' => $staffUserId,
                'direction' => $direction,
                'hikers_passed_count' => $booking->total_hikers,
                'notes' => $notes,
            ]);

            // Determine status
            $oldPos = $booking->current_pos;
            $booking->current_pos = $posName;

            if ($direction === 'ASCENDING') {
                if ($posName === 'Basecamp') {
                    if ($booking->status === BookingStatus::CONFIRMED) {
                        $booking->status = BookingStatus::CHECKED_IN;
                        $booking->checkin_at = now();
                    }
                } else {
                    $booking->status = BookingStatus::ON_TRAIL;
                }
            } elseif ($direction === 'DESCENDING') {
                if ($posName === 'Basecamp') {
                    $booking->status = BookingStatus::CHECKED_OUT;
                    $booking->checkout_at = now();
                } else {
                    $booking->status = BookingStatus::ON_TRAIL;
                }
            }

            $booking->save();

            return [
                'success' => true,
                'booking' => $booking->load(['members', 'ecoDeposit']),
                'checkpoint' => $checkpoint,
                'message' => "Berhasil memindai {$booking->leader_name} ({$booking->total_hikers} orang) di {$posName} [Arah: {$direction}].",
            ];
        });
    }

    public function createSosAlert(array $data): SosAlert
    {
        return SosAlert::create([
            'booking_id' => $data['booking_id'] ?? null,
            'reporter_name' => $data['reporter_name'],
            'reporter_phone' => $data['reporter_phone'],
            'pos_location' => $data['pos_location'],
            'alert_type' => SosType::from($data['alert_type'] ?? 'ACCIDENT'),
            'description' => $data['description'],
            'status' => 'ACTIVE',
        ]);
    }

    public function resolveSosAlert(int $sosId, string $evacuationNotes): SosAlert
    {
        $alert = SosAlert::findOrFail($sosId);
        $alert->update([
            'status' => 'RESOLVED',
            'evacuation_team_notes' => $evacuationNotes,
            'resolved_at' => now(),
        ]);

        return $alert;
    }

    public function verifyEcoDeposit(int $bookingId, array $returnedItems, ?int $staffUserId = null): EcoDeposit
    {
        $deposit = EcoDeposit::where('booking_id', $bookingId)->firstOrFail();

        $returnedPlastics = (int) ($returnedItems['returned_plastics_count'] ?? 0);
        $returnedCans = (int) ($returnedItems['returned_cans_count'] ?? 0);
        $returnedBottles = (int) ($returnedItems['returned_bottles_count'] ?? 0);

        $totalBrought = $deposit->total_brought_trash;
        $totalReturned = $returnedPlastics + $returnedCans + $returnedBottles;

        // Refund jika pendaki mengembalikan minimal 80% dari sampah yang dibawa
        // Edge case: jika tidak bawa sampah sama sekali (totalBrought = 0), langsung lolos
        $isAcceptable = $totalBrought === 0 || ($totalReturned >= (int) ceil($totalBrought * 0.8));
        $status = $isAcceptable ? 'REFUNDED' : 'FORFEITED_TRASH_LEFT';

        $deposit->update([
            'returned_plastics_count' => $returnedPlastics,
            'returned_cans_count' => $returnedCans,
            'returned_bottles_count' => $returnedBottles,
            'deposit_status' => $status,
            'checked_out_by' => $staffUserId,
            'refunded_at' => $isAcceptable ? now() : null,
        ]);

        return $deposit;
    }
}
