<?php

namespace One23\Helpers;

use Illuminate\Support\Arr as IlluminateArr;

class Arr
{
    public static function filterNull(array $array): array
    {
        return array_filter($array, fn($value) => $value !== null);
    }

    public static function inArray(
        mixed $needle,
        array $haystack,
        ?string $path = null
    ): bool {
        if (! $path) {
            return in_array($needle, $haystack, true);
        }

        foreach ($haystack as $value) {
            if (! is_array($value)) {
                continue;
            }

            $val = IlluminateArr::get($value, $path);
            if ($val === $needle) {
                return true;
            }
        }

        return false;
    }

    public static function search(
        mixed $needle,
        array $haystack,
        ?string $path = null
    ): false|int|string {
        if (! $path) {
            return array_search($needle, $haystack, true);
        }

        foreach ($haystack as $key => $value) {
            if (! is_array($value)) {
                continue;
            }

            $val = IlluminateArr::get($value, $path);
            if ($val === $needle) {
                return $key;
            }
        }

        return false;
    }
}
