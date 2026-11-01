<?php

namespace App\Shared\Enums;

enum SosType: string
{
    case MEDICAL = 'MEDICAL';
    case LOST = 'LOST';
    case HYPOTHERMIA = 'HYPOTHERMIA';
    case WEATHER_TRAPPED = 'WEATHER_TRAPPED';
    case ACCIDENT = 'ACCIDENT';

    public function label(): string
    {
        return match ($this) {
            self::MEDICAL => 'Kondisi Medis Kritis / Sakit Berat',
            self::LOST => 'Tersesat / Terpisah Rombongan',
            self::HYPOTHERMIA => 'Hipotermia Akut',
            self::WEATHER_TRAPPED => 'Terjebak Cuaca Ekstrem / Pohon Tumbang',
            self::ACCIDENT => 'Kecelakaan Fisik / Cedera Jalur',
        };
    }
}
