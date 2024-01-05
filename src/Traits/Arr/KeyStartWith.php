<?php

namespace One23\Helpers\Traits\Arr;

use One23\Helpers\Str;

trait KeyStartWith
{
    public static function keyStartWith(
        array $arr,
        string|array $needles,
        bool $caseSensitive = true
    ): array {
        $res = [];

        foreach ($arr as $key => $value) {
            if (is_int($key)) {
                continue;
            }

            if (! Str::startWith($key, $needles, $caseSensitive)) {
                continue;
            }

            $res[$key] = $value;
        }

        return $res;
    }
}
