<?php

namespace One23\Helpers\Traits\Number;

/**
 * @method static val
 * @method static all
 */
trait Uniq
{
    public static function uniq(mixed ...$args): array
    {
        $all = static::all(...$args);

        $uniq = array_unique($all, SORT_NUMERIC);

        return array_values($uniq);
    }
}
