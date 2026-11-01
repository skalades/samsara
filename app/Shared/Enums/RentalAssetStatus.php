<?php

namespace App\Shared\Enums;

enum RentalAssetStatus: string
{
    case TERSEDIA = 'TERSEDIA';
    case DISEWA = 'DISEWA';
    case PERLU_QC_CUCI = 'PERLU_QC_CUCI';
    case DALAM_PERBAIKAN = 'DALAM_PERBAIKAN';

    public function label(): string
    {
        return match ($this) {
            self::TERSEDIA => 'Tersedia di Rak',
            self::DISEWA => 'Sedang Dipakai Pendaki',
            self::PERLU_QC_CUCI => 'Perlu Dicuci / QC',
            self::DALAM_PERBAIKAN => 'Dalam Perbaikan / Rusak',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::TERSEDIA => 'emerald',
            self::DISEWA => 'sky',
            self::PERLU_QC_CUCI => 'amber',
            self::DALAM_PERBAIKAN => 'rose',
        };
    }
}
