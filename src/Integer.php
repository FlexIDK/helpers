<?php

namespace One23\Helpers;

class Integer
{
    use Traits\First;
    use Traits\Last;
    use Traits\Number\All;
    use Traits\Number\MinMax;
    use Traits\Number\Uniq;

    public static function val(mixed $val): ?int
    {
        $res = Number::val($val);

        return ! is_null($res)
            ? (int)$res
            : null;
    }

    public static function get(mixed $val = null, ?int $default = null, ?int $min = null, ?int $max = null): ?int
    {
        $val = static::val($val);

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
        return static::val($val);
    }

    public static function getOrZero(mixed $val): int
    {
        $val = static::val($val);

        return ! is_null($val)
            ? $val
            : 0;
    }
}
