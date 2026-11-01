<?php

namespace App\Shared\Enums;

enum BookingStatus: string
{
    case DRAFT = 'DRAFT';
    case PENDING_PAYMENT = 'PENDING_PAYMENT';
    case CONFIRMED = 'CONFIRMED';
    case CHECKED_IN = 'CHECKED_IN';
    case ON_TRAIL = 'ON_TRAIL';
    case CHECKED_OUT = 'CHECKED_OUT';
    case CANCELLED = 'CANCELLED';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PENDING_PAYMENT => 'Menunggu Pembayaran',
            self::CONFIRMED => 'Terkonfirmasi (Siap Berangkat)',
            self::CHECKED_IN => 'Check-in di Basecamp',
            self::ON_TRAIL => 'Sedang di Jalur Pendakian',
            self::CHECKED_OUT => 'Selesai Turun (Check-out)',
            self::CANCELLED => 'Dibatalkan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::PENDING_PAYMENT => 'amber',
            self::CONFIRMED => 'emerald',
            self::CHECKED_IN => 'sky',
            self::ON_TRAIL => 'indigo',
            self::CHECKED_OUT => 'green',
            self::CANCELLED => 'rose',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::DRAFT => 'bg-slate-100 text-slate-700 border border-slate-200',
            self::PENDING_PAYMENT => 'bg-amber-100 text-amber-800 border border-amber-300',
            self::CONFIRMED => 'bg-emerald-100 text-emerald-800 border border-emerald-300',
            self::CHECKED_IN => 'bg-sky-100 text-sky-800 border border-sky-300',
            self::ON_TRAIL => 'bg-indigo-100 text-indigo-800 border border-indigo-300',
            self::CHECKED_OUT => 'bg-green-100 text-green-800 border border-green-300',
            self::CANCELLED => 'bg-rose-100 text-rose-800 border border-rose-300',
        };
    }
}
