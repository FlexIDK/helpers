<?php

namespace One23\Helpers\Traits\Arr;

use One23\Helpers\Integer;

trait Preview
{
    public static function preview(
        array $arr,
        int $start = 3,
        int $end = 3,
        $separator = null,
    ): array {
        $res = [];

        $cnt = count($arr);
        $start = Integer::get($start, null, 0, $start);
        $end = Integer::get($end, null, 0, $end);

        //

        $size = ($start ?: 0) + ($end ?: 0) +
            ($start ? 1 : 0) + ($end ? 1 : 0);

        if (
            $size === 0 ||
            $cnt <= $size
        ) {
            return $arr;
        }

        if ($start) {
            $res = array_slice($arr, 0, $start);
        }

        if ($separator) {
            $res[] = $separator;
        }

        if ($end) {
            $res = array_merge($res, array_slice($arr, -$end));
        }

        return $res;
    }
}
