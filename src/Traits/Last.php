<?php

namespace One23\Helpers\Traits;

/**
 * @method static val
 */
trait Last
{
    public static function last(...$args): mixed
    {
        foreach (array_reverse($args) as $val) {
            $val = static::val($val);
            if (is_null($val)) {
                continue;
            }

            return $val;
        }

        return null;
    }
}
