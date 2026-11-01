<?php

namespace App\Shared\Enums;

enum ProductCategory: string
{
    case LOGISTIK = 'LOGISTIK';
    case PERLENGKAPAN = 'PERLENGKAPAN';
    case MAKANAN_MINUMAN = 'MAKANAN_MINUMAN';
    case PRODUK_LOKAL = 'PRODUK_LOKAL';
    case GAS_BAHAN_BAKAR = 'GAS_BAHAN_BAKAR';

    public function label(): string
    {
        return match ($this) {
            self::LOGISTIK => 'Logistik & Nutrisi',
            self::PERLENGKAPAN => 'Perlengkapan Jalur',
            self::MAKANAN_MINUMAN => 'Makanan & Minuman Siap Santap',
            self::PRODUK_LOKAL => 'Produk Khas Warga Cigedug',
            self::GAS_BAHAN_BAKAR => 'Gas & Bahan Bakar',
        };
    }
}
