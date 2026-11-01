<?php

namespace App\Shared\Enums;

enum OjekQueueStatus: string
{
    case WAITING = 'WAITING';
    case CALLED = 'CALLED';
    case ON_TRIP = 'ON_TRIP';
    case COMPLETED = 'COMPLETED';
    case CANCELLED = 'CANCELLED';

    public function label(): string
    {
        return match ($this) {
            self::WAITING => 'Menunggu Antrean',
            self::CALLED => 'Driver Dipanggil',
            self::ON_TRIP => 'Dalam Perjalanan',
            self::COMPLETED => 'Selesai Sampai Tujuan',
            self::CANCELLED => 'Dibatalkan',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::WAITING => 'bg-amber-100 text-amber-800 border-amber-300',
            self::CALLED => 'bg-sky-100 text-sky-800 border-sky-300 animate-pulse',
            self::ON_TRIP => 'bg-blue-100 text-blue-800 border-blue-300',
            self::COMPLETED => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            self::CANCELLED => 'bg-rose-100 text-rose-800 border-rose-300',
        };
    }
}
