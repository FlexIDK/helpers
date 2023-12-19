<?php

namespace One23\Helpers;

class Number
{
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

        $val = (string)$val;
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

    public static function first(...$args): float|int|null
    {
        foreach ($args as $val) {
            $val = static::val($val);
            if (is_null($val)) {
                continue;
            }

            return $val;
        }

        return null;
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

    /**
     * @return array<float|int>
     */
    public static function all(mixed ...$args): array
    {
        $res = [];
        foreach ($args as $val) {
            $val = static::val($val);
            if (is_null($val)) {
                continue;
            }

            $res[] = $val;
        }

        return $res;
    }

    public static function min(mixed ...$args): float|int|null
    {
        $arr = static::all(...$args);
        if (empty($arr)) {
            return null;
        }

        return min(...$arr);
    }

    public static function max(mixed ...$args): float|int|null
    {
        $arr = static::all(...$args);
        if (empty($arr)) {
            return null;
        }

        return max(...$arr);
    }
}
