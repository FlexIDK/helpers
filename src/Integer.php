<?php

namespace One23\Helpers;

class Integer
{
    public static function val(mixed $val): ?int
    {
        $res = Number::val($val);

        return ! is_null($res)
            ? (int)$res
            : null;
    }

    public static function first(mixed ...$args): ?int
    {
        $res = Number::first(...$args);

        return ! is_null($res)
            ? (int)$res
            : null;
    }

    public static function get(mixed $val = null, ?int $default = null, ?int $min = null, ?int $max = null): ?int
    {
        $res = Number::get(
            $val,
            $default,
            $min,
            $max
        );

        return ! is_null($res)
            ? (int)$res
            : $default;
    }

    public static function getOrNull(mixed $val): ?int
    {
        return static::get($val);
    }

    public static function getOrZero(mixed $val): int
    {
        return static::get($val, 0);
    }
}
