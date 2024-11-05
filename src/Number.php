<?php

namespace One23\Helpers;

class Number
{
    use Traits\First;
    use Traits\Last;
    use Traits\Number\All;
    use Traits\Number\MinMax;
    use Traits\Number\Uniq;

    public static function val(mixed $val): float|int|null
    {
        $val = Value::val($val);
        if (
            is_null($val)
            || is_int($val)
            || is_float($val)
        ) {
            return $val;
        }

        if (is_bool($val)) {
            return $val ? 1 : 0;
        }

        if (! is_string($val)) {
            return null;
        }

        //

        return static::str2val($val);
    }

    protected static function str2val(string $val): int|float|null
    {
        // number charset
        if (! preg_match('/[0-9"\',.`\s-]+/', $val, $match)) {
            return null;
        }

        $val = $match[0];
        $val = str_replace(',', '.', $val);

        //

        [$int, $dec] = explode('.', $val, 2) + ['', ''];

        // int

        preg_match('/(-)?([0-9"\-`\'\s]+)/', $int, $match);
        $negative = (bool)($match[1] ?? false);

        if (str_contains(($match[2] ?? ''), '-')) {
            preg_match('/[0-9"`\'\s]+/', $match[2], $match);
            $int = $match[0] ?? '';
            $dec = '';
        } else {
            $int = $match[2] ?? '';
        }

        preg_match_all('/[0-9]+/', $int, $match);
        $int = implode('', $match[0]);

        // dec

        if (preg_match('/[0-9"\'\s]+/', $dec, $match)) {
            preg_match_all('/[0-9]+/', $match[0], $match);
            $dec = implode('', $match[0]);
        } else {
            $dec = '';
        }

        if ($int === '' && $dec === '') {
            return null;
        }

        $int = (int)$int ?: 0;
        $dec = (float)('0.' . ($dec ?: 0));

        if ($dec) {
            $val = ($int + $dec) * ($negative ? -1 : 1);
        } else {
            $val = ($int) * ($negative ? -1 : 1);
        }

        return $val;
    }

    public static function int(
        mixed $val = null,
        ?int $default = null,
        ?int $min = null,
        ?int $max = null
    ): ?int {
        return Integer::get($val, $default, $min, $max);
    }

    public static function float(
        mixed $val = null,
        ?float $default = null,
        ?float $min = null,
        ?float $max = null
    ): ?float {
        $res = Number::get($val, $default, $min, $max);

        return ! is_null($res)
            ? (float)$res
            : null;
    }

    public static function money(mixed $val = null, int $number = 2): float|int|null
    {
        $float = static::get($val, null, 0);
        if (! $float) {
            return 0;
        }

        $pow = pow(10, $number);
        $int = (int)floor($float * $pow);

        return $int / $pow;
    }

    public static function get($val = null, float|int|null $default = null, float|int|null $min = null, float|int|null $max = null): float|int|null
    {
        $val = static::val($val);
        if (! is_numeric($val)) {
            return $default;
        }

        //

        $val = (float)$val;
        if (! is_null($min) && $val < $min) {
            $val = null;
        }

        if (! is_null($max) && $val > $max) {
            $val = null;
        }

        //

        if (! is_null($val)) {
            return $val;
        }

        return $default;
    }
}
