<?php

namespace App\Shared\Enums;

enum PosItemType: string
{
    case SIMAKSI        = 'SIMAKSI';
    case STORE_BELI_PUTUS  = 'STORE_BELI_PUTUS';
    case STORE_KONSINYASI  = 'STORE_KONSINYASI';
    case RENTAL         = 'RENTAL';
    case RENTAL_DEPOSIT = 'RENTAL_DEPOSIT';
    case ECO_DEPOSIT    = 'ECO_DEPOSIT';
    case OJEK           = 'OJEK';

    public function label(): string
    {
        return match ($this) {
            self::SIMAKSI           => 'Tiket SIMAKSI Pendakian',
            self::STORE_BELI_PUTUS  => 'Produk Toko (Beli Putus)',
            self::STORE_KONSINYASI  => 'Produk Titip Jual UMKM',
            self::RENTAL            => 'Sewa Perlengkapan',
            self::RENTAL_DEPOSIT    => 'Deposit Jaminan Sewa',
            self::ECO_DEPOSIT       => 'Jaminan Sampah (Eco-Deposit)',
            self::OJEK              => 'Tiket Ojek Pangkalan',
        };
    }

    public function isStore(): bool
    {
        return in_array($this, [self::STORE_BELI_PUTUS, self::STORE_KONSINYASI], true);
    }

    public function isConsignment(): bool
    {
        return $this === self::STORE_KONSINYASI;
    }
}
