<?php

namespace App\Support;

final class Money
{
    public static function of(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '0.00';
        }

        return self::fromCents(self::toCents((string) $value));
    }

    public static function add(string $left, string $right): string
    {
        return self::fromCents(self::toCents($left) + self::toCents($right));
    }

    public static function multiply(string $amount, int $quantity): string
    {
        return self::fromCents(self::toCents($amount) * $quantity);
    }

    public static function toCents(string $amount): int
    {
        $amount = trim($amount);
        $negative = str_starts_with($amount, '-');
        $amount = ltrim($amount, '+-');

        [$major, $minor] = array_pad(explode('.', $amount, 2), 2, '0');
        $major = $major === '' ? '0' : $major;
        $minor = str_pad(substr($minor, 0, 2), 2, '0');

        $cents = ((int) $major) * 100 + (int) $minor;

        return $negative ? -$cents : $cents;
    }

    public static function fromCents(int $cents): string
    {
        $negative = $cents < 0;
        $cents = abs($cents);

        return ($negative ? '-' : '').sprintf('%d.%02d', intdiv($cents, 100), $cents % 100);
    }

    public static function format(mixed $value, string $currency = 'MAD'): string
    {
        return number_format((float) self::of($value), 2, '.', ',').' '.$currency;
    }

    public static function isZero(mixed $value): bool
    {
        return self::toCents(self::of($value)) === 0;
    }
}
