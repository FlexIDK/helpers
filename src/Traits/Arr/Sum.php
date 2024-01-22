<?php

namespace One23\Helpers\Traits\Arr;

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

                if (
                    is_array($val) ||
                    is_string($val)
                ) {
                    if (! is_array($res[$key])) {
                        $res[$key] = [];
                    }

                    if (! empty($val)) {
                        $res[$key][] = $val;
                    }
                }
            }
        }

        return $res;
    }
}
