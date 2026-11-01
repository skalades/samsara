<?php

namespace App\Shared\Enums;

enum OjekDriverStatus: string
{
    case STANDBY = 'STANDBY';
    case CALLED = 'CALLED';
    case ON_TRIP = 'ON_TRIP';
    case BREAK = 'BREAK';
    case OFFLINE = 'OFFLINE';

    public function label(): string
    {
        return match ($this) {
            self::STANDBY => 'Siap / Standby',
            self::CALLED => 'Dipanggil',
            self::ON_TRIP => 'Sedang Mengantar',
            self::BREAK => 'Istirahat',
            self::OFFLINE => 'Nonaktif / Pulang',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::STANDBY => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            self::CALLED => 'bg-amber-100 text-amber-800 border-amber-300 animate-pulse',
            self::ON_TRIP => 'bg-blue-100 text-blue-800 border-blue-300',
            self::BREAK => 'bg-purple-100 text-purple-800 border-purple-300',
            self::OFFLINE => 'bg-slate-100 text-slate-600 border-slate-300',
        };
    }
}
