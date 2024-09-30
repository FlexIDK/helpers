<?php

namespace One23\Helpers\Traits\Number;

/**
 * @method static uniq
 */
trait MinMax
{
    public static function min(mixed ...$args): float|int|null
    {
        $arr = static::uniq(...$args);
        if (empty($arr)) {
            return null;
        }

        if (count($arr) === 1) {
            return $arr[0];
        }

        return min(...$arr);
    }

    public static function max(mixed ...$args): float|int|null
    {
        $arr = static::uniq(...$args);
        if (empty($arr)) {
            return null;
        }

        if (count($arr) === 1) {
            return $arr[0];
        }

        return max(...$arr);
    }

    /**
     * @return float[]|int[]|null
     */
    public static function minMax(mixed ...$args): ?array
    {
        $arr = static::uniq(...$args);
        if (empty($arr)) {
            return null;
        }

        if (count($arr) === 1) {
            return [$arr[0], $arr[0]];
        }

        return [static::min(...$arr), static::max(...$arr)];
    }
}
