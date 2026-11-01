<?php

namespace App\Shared\Enums;

enum TrailStatus: string
{
    case OPEN = 'OPEN';
    case WARNING = 'WARNING';
    case CLOSED = 'CLOSED';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Buka Normal',
            self::WARNING => 'Waspada Cuaca / Hati-hati',
            self::CLOSED => 'Jalur Ditutup Darurat',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::OPEN => 'bg-emerald-500 text-white',
            self::WARNING => 'bg-amber-500 text-white',
            self::CLOSED => 'bg-rose-600 text-white animate-pulse',
        };
    }
}
