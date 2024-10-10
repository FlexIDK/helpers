<?php

namespace One23\Helpers\Traits\Arr;

use Illuminate\Support\Arr as IlluminateArr;

trait FirstByKeys
{
    public static function firstByKeys(
        array $array,
        array|string $keys,
        mixed $default = null,
        bool $skipEmpty = false,
    ): mixed {
        $keys = IlluminateArr::wrap($keys);
        $keys = IlluminateArr::flatten($keys);

        foreach ($keys as $key) {
            if (! IlluminateArr::has($array, $key)) {
                continue;
            }

            $val = IlluminateArr::get($array, $key);
            if (
                $skipEmpty &&
                empty($val)
            ) {
                continue;
            }

            return $val;
        }

        return $default;
    }
}
