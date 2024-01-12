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

        return min(...$arr);
    }

    public static function max(mixed ...$args): float|int|null
    {
        $arr = static::uniq(...$args);
        if (empty($arr)) {
            return null;
        }

        return max(...$arr);
    }
}
