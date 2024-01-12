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

        $val = preg_replace("@\s+@", '', $val);
        $val = str_replace(',', '.', $val);

        if (
            $val === ''
            || ! is_numeric($val)
        ) {
            return null;
        }

        if (! str_contains($val, '.')) {
            return (int)$val;
        }

        return (float)$val;
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

        return floor($float * $pow) / $pow;
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
