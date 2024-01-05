<?php

namespace One23\Helpers\Traits\Arr;

use Illuminate\Support\Str as IlluminateStr;
use One23\Helpers\Arr;
use One23\Helpers\Integer;

trait Substr
{
    public static function substr(
        array $arr,
        ?int $limit = null,
        string $end = '...'
    ): string {
        $arr = Arr::filterNull($arr);
        $json = json_encode($arr, JSON_UNESCAPED_UNICODE);

        $limit = Integer::get($limit, null, 0);
        if (! $limit) {
            return $json;
        }

        return IlluminateStr::limit($json, $limit, $end);
    }
}
