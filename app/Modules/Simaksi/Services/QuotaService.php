<?php

namespace App\Modules\Simaksi\Services;

use App\Modules\Simaksi\Models\DailyQuota;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class QuotaService
{
    public function getMonthlyCalendar(int $year, int $month): Collection
    {
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $existingQuotas = DailyQuota::whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->keyBy(fn ($q) => $q->date->toDateString());

        $days = collect();
        $current = $start->copy();

        while ($current->lte($end)) {
            $dateStr = $current->toDateString();
            $isWeekend = $current->isWeekend();
            $defaultMax = $isWeekend ? 300 : 150;

            if (isset($existingQuotas[$dateStr])) {
                $quota = $existingQuotas[$dateStr];
            } else {
                $quota = new DailyQuota([
                    'date' => $dateStr,
                    'max_quota' => $defaultMax,
                    'booked_count' => 0,
                    'is_blackout' => false,
                ]);
            }

            $days->push([
                'date' => $dateStr,
                'day_number' => $current->day,
                'day_name' => $current->locale('id')->isoFormat('ddd'),
                'is_weekend' => $isWeekend,
                'is_past' => $current->isPast() && ! $current->isToday(),
                'is_today' => $current->isToday(),
                'max_quota' => $quota->max_quota,
                'booked_count' => $quota->booked_count,
                'remaining' => $quota->remaining_quota,
                'percentage' => $quota->percentage_used,
                'is_blackout' => $quota->is_blackout,
                'notes' => $quota->notes,
            ]);

            $current->addDay();
        }

        return $days;
    }

    public function setBlackout(string $date, bool $isBlackout, ?string $notes = null): DailyQuota
    {
        $quota = DailyQuota::firstOrCreate(
            ['date' => $date],
            [
                'max_quota' => Carbon::parse($date)->isWeekend() ? 300 : 150,
                'booked_count' => 0,
            ]
        );

        $quota->update([
            'is_blackout' => $isBlackout,
            'notes' => $notes,
        ]);

        return $quota;
    }
}
