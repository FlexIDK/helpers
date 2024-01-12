<?php

namespace One23\Helpers\Traits\Number;

/**
 * @method static val
 */
trait All
{
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
}
