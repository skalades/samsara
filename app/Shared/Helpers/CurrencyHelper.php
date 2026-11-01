<?php

namespace App\Shared\Helpers;

class CurrencyHelper
{
    /**
     * Parse raw currency input into clean float, safely handling Indonesian thousands separator (.).
     * E.g. "500.000" -> 500000.0, "Rp 1.500.000" -> 1500000.0
     */
    public static function parse(mixed $value): float
    {
        if (is_null($value) || $value === '') {
            return 0.0;
        }

        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        $str = trim((string) $value);
        $str = str_ireplace(['rp', ' ', 'idr'], '', $str);

        if (str_contains($str, ',') && str_contains($str, '.')) {
            $str = str_replace('.', '', $str);
            $str = str_replace(',', '.', $str);
        } elseif (str_contains($str, '.')) {
            $str = str_replace('.', '', $str);
        } elseif (str_contains($str, ',')) {
            $str = str_replace(',', '.', $str);
        }

        $clean = preg_replace('/[^0-9.]/', '', $str);

        return (float) $clean;
    }

    /**
     * Format a numeric amount into standard Indonesian Rupiah format (e.g. 500000 -> "500.000").
     */
    public static function format(mixed $value, bool $withPrefix = false): string
    {
        $numeric = self::parse($value);
        $formatted = number_format($numeric, 0, ',', '.');

        return $withPrefix ? "Rp {$formatted}" : $formatted;
    }
}
