<?php

namespace App\Shared\Enums;

enum RentalCondition: string
{
    case BAIK = 'BAIK';
    case RUSAK_RINGAN = 'RUSAK_RINGAN';
    case RUSAK_BERAT = 'RUSAK_BERAT';
    case HILANG = 'HILANG';

    public function label(): string
    {
        return match ($this) {
            self::BAIK => 'Kondisi Baik (Sesuai Standar)',
            self::RUSAK_RINGAN => 'Rusak Ringan (Robek Kecil/Kotor)',
            self::RUSAK_BERAT => 'Rusak Berat (Frame Patah/Sobek)',
            self::HILANG => 'Hilang / Tidak Kembali',
        };
    }
}
