<?php

namespace App\Shared\Enums;

enum WeatherStatus: string
{
    case CERAH = 'CERAH';
    case BERAWAN = 'BERAWAN';
    case HUJAN_RINGAN = 'HUJAN_RINGAN';
    case HUJAN_LEBAT = 'HUJAN_LEBAT';
    case BADAI = 'BADAI';

    public function label(): string
    {
        return match ($this) {
            self::CERAH => 'Cerah Berawan',
            self::BERAWAN => 'Mendung / Berkabut',
            self::HUJAN_RINGAN => 'Hujan Ringan',
            self::HUJAN_LEBAT => 'Hujan Lebat & Angin',
            self::BADAI => 'Badai Ekstrem & Petir',
        };
    }
}
