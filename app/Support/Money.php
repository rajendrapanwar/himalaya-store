<?php

namespace App\Support;

final class Money
{
    public static function toPaise(int|float|string|null $rupees): int
    {
        return (int) round(((float) ($rupees ?? 0)) * 100);
    }

    public static function toRupees(int $paise): float
    {
        return round($paise / 100, 2);
    }

    public static function percentage(int $paise, int $basisPoints): int
    {
        return intdiv(($paise * $basisPoints) + 5000, 10000);
    }
}
