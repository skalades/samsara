<?php

namespace App\Shared\Enums;

enum InventoryType: string
{
    case BELI_PUTUS = 'BELI_PUTUS';
    case KONSINYASI = 'KONSINYASI';

    public function label(): string
    {
        return match ($this) {
            self::BELI_PUTUS => 'Beli Putus (SAMSARA)',
            self::KONSINYASI => 'Titip Jual (UMKM Lokal)',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::BELI_PUTUS => 'emerald',
            self::KONSINYASI => 'amber',
        };
    }
}
