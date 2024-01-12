<?php

namespace One23\Helpers\Traits;

/**
 * @method static val
 */
trait First
{
    public static function first(...$args): mixed
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
}
