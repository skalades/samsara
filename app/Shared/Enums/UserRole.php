<?php

namespace App\Shared\Enums;

enum UserRole: string
{
    case SUPERADMIN = 'SUPERADMIN';
    case ADMIN_SAR = 'ADMIN_SAR';
    case KASIR = 'KASIR';
    case PETUGAS_POS = 'PETUGAS_POS';
    case PENDAKI = 'PENDAKI';

    public function label(): string
    {
        return match ($this) {
            self::SUPERADMIN => 'Superadmin Pengelola',
            self::ADMIN_SAR => 'Koordinator Lapangan & SAR',
            self::KASIR => 'Petugas Kasir Basecamp',
            self::PETUGAS_POS => 'Petugas Pos Jalur (Pos 1-4)',
            self::PENDAKI => 'Pendaki / Pengunjung',
        };
    }
}
