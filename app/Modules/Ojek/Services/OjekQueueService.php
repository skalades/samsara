<?php

namespace App\Modules\Ojek\Services;

use App\Modules\Ojek\Models\OjekDriver;
use App\Modules\Ojek\Models\OjekDriverAttendance;
use App\Modules\Ojek\Models\OjekQueueTicket;
use App\Modules\Ojek\Models\OjekRoute;
use App\Shared\Enums\OjekDriverStatus;
use App\Shared\Enums\OjekQueueStatus;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class OjekQueueService
{
    /**
     * Check-in driver harian ke dalam antrean pangkalan (Fair FIFO)
     */
    public function checkInDriver(int $driverId, ?string $date = null): OjekDriverAttendance
    {
        $date = $date ?? today()->toDateString();
        $driver = OjekDriver::findOrFail($driverId);

        if (! $driver->is_active) {
            throw new Exception("Driver {$driver->name} sedang nonaktif di sistem.");
        }

        return DB::transaction(function () use ($driver, $date) {
            $existing = OjekDriverAttendance::where('ojek_driver_id', $driver->id)
                ->whereDate('date', $date)
                ->first();

            if ($existing) {
                if ($existing->status === OjekDriverStatus::OFFLINE || $existing->status === OjekDriverStatus::BREAK) {
                    $maxOrder = (int) OjekDriverAttendance::whereDate('date', $date)->max('queue_order');
                    $existing->update([
                        'status' => OjekDriverStatus::STANDBY,
                        'queue_order' => $maxOrder + 1,
                    ]);
                }
                return $existing->fresh(['driver']);
            }

            $maxOrder = (int) OjekDriverAttendance::whereDate('date', $date)->max('queue_order');

            return OjekDriverAttendance::create([
                'ojek_driver_id' => $driver->id,
                'date' => $date,
                'queue_order' => $maxOrder + 1,
                'status' => OjekDriverStatus::STANDBY,
                'checked_in_at' => now(),
                'daily_trips_count' => 0,
            ])->load('driver');
        });
    }

    /**
     * Ubah status driver (Break / Istirahat atau Offline)
     */
    public function updateDriverStatus(int $attendanceId, OjekDriverStatus $status): OjekDriverAttendance
    {
        $attendance = OjekDriverAttendance::findOrFail($attendanceId);

        return DB::transaction(function () use ($attendance, $status) {
            if ($status === OjekDriverStatus::STANDBY && in_array($attendance->status, [OjekDriverStatus::BREAK, OjekDriverStatus::OFFLINE])) {
                // Saat kembali standby, taruh di ekor antrean
                $maxOrder = (int) OjekDriverAttendance::whereDate('date', $attendance->date)->max('queue_order');
                $attendance->update([
                    'status' => $status,
                    'queue_order' => $maxOrder + 1,
                ]);
            } else {
                $attendance->update(['status' => $status]);
            }

            return $attendance->fresh(['driver']);
        });
    }

    /**
     * Buat tiket antrean penumpang (dari Kasir POS atau Walk-In Pangkalan)
     */
    public function createTicket(array $data): OjekQueueTicket
    {
        return DB::transaction(function () use ($data) {
            $route = OjekRoute::findOrFail($data['ojek_route_id']);
            $datePrefix = now()->format('ymd');

            // Cari nomor urut terakhir hari ini secara aman untuk mencegah duplikasi
            $latestTicket = OjekQueueTicket::whereDate('created_at', today())
                ->where('ticket_number', 'like', "OJ-{$datePrefix}-%")
                ->lockForUpdate()
                ->orderByDesc('id')
                ->first();

            $nextSeq = 1;
            if ($latestTicket && preg_match('/-(\d+)$/', $latestTicket->ticket_number, $matches)) {
                $nextSeq = ((int) $matches[1]) + 1;
            }

            // Loop fallback jika nomor urut sudah terpakai
            do {
                $ticketNumber = sprintf('OJ-%s-%03d', $datePrefix, $nextSeq++);
            } while (OjekQueueTicket::where('ticket_number', $ticketNumber)->exists());

            $ticket = OjekQueueTicket::create([
                'ticket_number' => $ticketNumber,
                'ojek_route_id' => $route->id,
                'passenger_name' => $data['passenger_name'],
                'passenger_phone' => $data['passenger_phone'] ?? null,
                'simaksi_booking_id' => $data['simaksi_booking_id'] ?? null,
                'pos_order_id' => $data['pos_order_id'] ?? null,
                'source' => $data['source'] ?? 'PANGKALAN_WALKIN',
                'fare_amount' => $route->fare_amount,
                'payment_status' => $data['payment_status'] ?? 'PAID',
                'payment_method' => $data['payment_method'] ?? 'CASH',
                'status' => OjekQueueStatus::WAITING,
                'notes' => $data['notes'] ?? null,
            ]);

            return $ticket->load('route');
        });
    }

    /**
     * Panggil giliran berikutnya (Memasangkan Tiket Menunggu paling awal dengan Driver Standby terdepan)
     */
    public function callNextQueue(?int $ticketId = null, ?int $attendanceId = null): ?OjekQueueTicket
    {
        return DB::transaction(function () use ($ticketId, $attendanceId) {
            $today = today()->toDateString();

            // 1. Dapatkan tiket
            if ($ticketId) {
                $ticket = OjekQueueTicket::where('id', $ticketId)
                    ->where('status', OjekQueueStatus::WAITING)
                    ->lockForUpdate()
                    ->first();
            } else {
                $ticket = OjekQueueTicket::where('status', OjekQueueStatus::WAITING)
                    ->whereDate('created_at', today())
                    ->orderBy('id', 'asc')
                    ->lockForUpdate()
                    ->first();
            }

            if (! $ticket) {
                throw new Exception('Tidak ada antrean penumpang yang sedang menunggu saat ini.');
            }

            // 2. Dapatkan driver standby terdepan
            if ($attendanceId) {
                $attendance = OjekDriverAttendance::where('id', $attendanceId)
                    ->where('status', OjekDriverStatus::STANDBY)
                    ->lockForUpdate()
                    ->first();
            } else {
                $attendance = OjekDriverAttendance::whereDate('date', $today)
                    ->where('status', OjekDriverStatus::STANDBY)
                    ->orderBy('queue_order', 'asc')
                    ->lockForUpdate()
                    ->first();
            }

            if (! $attendance) {
                throw new Exception('Semua driver sedang dalam perjalanan atau belum standby.');
            }

            // 3. Pasangkan dan update status
            $now = now();
            $ticket->update([
                'status' => OjekQueueStatus::CALLED,
                'assigned_driver_id' => $attendance->ojek_driver_id,
                'assigned_attendance_id' => $attendance->id,
                'called_at' => $now,
            ]);

            $attendance->update([
                'status' => OjekDriverStatus::CALLED,
                'last_called_at' => $now,
            ]);

            return $ticket->fresh(['route', 'driver', 'attendance.driver']);
        });
    }

    /**
     * Driver & Penumpang berangkat (ON_TRIP)
     */
    public function startTrip(int $ticketId): OjekQueueTicket
    {
        return DB::transaction(function () use ($ticketId) {
            $ticket = OjekQueueTicket::with(['attendance', 'driver'])->findOrFail($ticketId);

            if ($ticket->status !== OjekQueueStatus::CALLED) {
                throw new Exception("Tiket {$ticket->ticket_number} belum dalam status dipanggil.");
            }

            $now = now();
            $ticket->update([
                'status' => OjekQueueStatus::ON_TRIP,
                'started_at' => $now,
            ]);

            if ($ticket->attendance) {
                $ticket->attendance->update([
                    'status' => OjekDriverStatus::ON_TRIP,
                ]);
            }

            return $ticket->fresh(['route', 'driver']);
        });
    }

    /**
     * Selesai mengantar sampai tujuan (COMPLETED).
     * Driver otomatis kembali ke antrean giliran paling belakang (Fair Play FIFO).
     */
    public function completeTrip(int $ticketId): OjekQueueTicket
    {
        return DB::transaction(function () use ($ticketId) {
            $ticket = OjekQueueTicket::with(['attendance', 'driver'])->findOrFail($ticketId);

            if ($ticket->status !== OjekQueueStatus::ON_TRIP && $ticket->status !== OjekQueueStatus::CALLED) {
                throw new Exception("Tiket {$ticket->ticket_number} tidak sedang aktif dalam perjalanan.");
            }

            $now = now();
            $ticket->update([
                'status' => OjekQueueStatus::COMPLETED,
                'completed_at' => $now,
            ]);

            if ($ticket->attendance) {
                // Masukkan driver ke ekor antrean hari ini
                $maxOrder = (int) OjekDriverAttendance::whereDate('date', $ticket->attendance->date)->max('queue_order');

                $ticket->attendance->update([
                    'status' => OjekDriverStatus::STANDBY,
                    'queue_order' => $maxOrder + 1,
                    'last_trip_completed_at' => $now,
                    'daily_trips_count' => $ticket->attendance->daily_trips_count + 1,
                ]);
            }

            if ($ticket->driver) {
                $ticket->driver->increment('total_trips');
            }

            return $ticket->fresh(['route', 'driver']);
        });
    }

    /**
     * Batalkan tiket antrean
     */
    public function cancelTicket(int $ticketId, ?string $reason = null): OjekQueueTicket
    {
        return DB::transaction(function () use ($ticketId, $reason) {
            $ticket = OjekQueueTicket::with(['attendance', 'driver'])->findOrFail($ticketId);

            // Jika driver sudah dipanggil tapi tiket dibatalkan:
            // kembalikan driver ke posisi PERTAMA antrean (antrian prioritas) karena bukan salahnya
            if ($ticket->attendance && $ticket->status === OjekQueueStatus::CALLED) {
                // Geser semua driver standby hari ini ke belakang satu posisi untuk memberi ruang
                OjekDriverAttendance::whereDate('date', $ticket->attendance->date)
                    ->where('status', OjekDriverStatus::STANDBY)
                    ->increment('queue_order');

                $ticket->attendance->update([
                    'status'      => OjekDriverStatus::STANDBY,
                    'queue_order' => 1, // Driver yang batalkan tiket langsung ke antrian pertama
                ]);
            }

            $ticket->update([
                'status' => OjekQueueStatus::CANCELLED,
                'notes' => $reason ? ($ticket->notes ? "{$ticket->notes} | Batal: {$reason}" : "Batal: {$reason}") : $ticket->notes,
            ]);

            return $ticket;
        });
    }

    /**
     * Data real-time untuk Display TV & Mobile Koordinator
     */
    public function getRealtimeDashboardData(): array
    {
        $today = today()->toDateString();

        // 1. Tiket yang sedang dipanggil (aktif di layar utama panggilan)
        $latestCalled = OjekQueueTicket::with(['route', 'driver'])
            ->whereDate('created_at', today())
            ->where('status', OjekQueueStatus::CALLED)
            ->latest('called_at')
            ->first();

        // 2. Antrean Driver Standby (Urutan giliran FIFO)
        $standbyDrivers = OjekDriverAttendance::with('driver')
            ->whereDate('date', $today)
            ->where('status', OjekDriverStatus::STANDBY)
            ->orderBy('queue_order', 'asc')
            ->get();

        // 3. Driver yang sedang dalam perjalanan (ON_TRIP)
        $onTripDrivers = OjekDriverAttendance::with(['driver', 'tickets' => function ($q) {
            $q->where('status', OjekQueueStatus::ON_TRIP)->with('route');
        }])
            ->whereDate('date', $today)
            ->where('status', OjekDriverStatus::ON_TRIP)
            ->get();

        // 4. Antrean Penumpang yang Menunggu
        $waitingTickets = OjekQueueTicket::with('route')
            ->whereDate('created_at', today())
            ->where('status', OjekQueueStatus::WAITING)
            ->orderBy('id', 'asc')
            ->get();

        // 5. Riwayat Tiket Selesai Hari Ini
        $completedTickets = OjekQueueTicket::with(['route', 'driver'])
            ->whereDate('created_at', today())
            ->where('status', OjekQueueStatus::COMPLETED)
            ->latest('completed_at')
            ->take(10)
            ->get();

        // 6. Ringkasan Statistik Finansial & Operasional Hari Ini
        $totalRevenueToday = (float) OjekQueueTicket::whereDate('created_at', today())
            ->whereIn('status', [OjekQueueStatus::CALLED, OjekQueueStatus::ON_TRIP, OjekQueueStatus::COMPLETED])
            ->sum('fare_amount');

        $totalCompletedTrips = OjekQueueTicket::whereDate('created_at', today())
            ->where('status', OjekQueueStatus::COMPLETED)
            ->count();

        $activeDriversCount = OjekDriverAttendance::whereDate('date', $today)
            ->whereIn('status', [OjekDriverStatus::STANDBY, OjekDriverStatus::CALLED, OjekDriverStatus::ON_TRIP])
            ->count();

        // Perkiraan estimasi bagi hasil paguyuban Cigedug vs driver
        $routes = OjekRoute::where('is_active', true)->get();

        return [
            'latest_called' => $latestCalled,
            'standby_drivers' => $standbyDrivers,
            'on_trip_drivers' => $onTripDrivers,
            'waiting_tickets' => $waitingTickets,
            'completed_tickets' => $completedTickets,
            'summary' => [
                'total_revenue' => $totalRevenueToday,
                'total_completed_trips' => $totalCompletedTrips,
                'waiting_count' => $waitingTickets->count(),
                'active_drivers_count' => $activeDriversCount,
                'standby_drivers_count' => $standbyDrivers->count(),
                'on_trip_count' => $onTripDrivers->count(),
            ],
            'routes' => $routes,
        ];
    }
}
