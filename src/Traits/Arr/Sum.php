<?php

namespace One23\Helpers\Traits\Arr;

use Illuminate\Support\Str as IlluminateStr;
use One23\Helpers\Arr;
use One23\Helpers\Integer;

trait Sum
{
    public static function sum(
        array ...$arr,
    ): array {
        $res = [];

        foreach ($arr as $item) {
            foreach ($item as $key => $val) {
                if (is_numeric($key)) {
                    continue;
                }

                if (! isset($res[$key])) {
                    $res[$key] = null;
                }

                if (is_numeric($val)) {
                    $res[$key] += $val;
                }
            }
        }

        return $res;
    }
}
