<?php

namespace One23\Helpers;

use Illuminate\Support\Arr as IlluminateArr;

class Arr
{
    public static function flat(mixed $val, $delimiter = ','): array
    {
        $arr = Value::val($val);
        if (! $arr) {
            return [];
        }

        $arr = IlluminateArr::wrap($arr);
        $arr = IlluminateArr::flatten($arr);
        $arr = array_map(function($val) use ($delimiter) {
            if (is_numeric($val)) {
                return Number::val($val);
            }

            if (! is_string($val)) {
                return null;
            }

            $val = trim($val);
            if (str_contains($val, $delimiter)) {
                return array_map(
                    function($item) {
                        return is_numeric($item)
                            ? Number::val($item)
                            : Str::val($item);
                    },
                    explode($delimiter, $val)
                );
            }

            return $val;
        }, $arr);
        $arr = IlluminateArr::flatten($arr);

        $arr = static::filterNull($arr);

        return array_values(
            array_unique($arr, SORT_REGULAR)
        );
    }

    public static function ids(mixed $val, ?int $min = null, ?int $max = null): array
    {
        $arr = static::flat($val);

        $arr = array_map(
            fn($item) => Number::int($item, null, $min, $max),
            $arr
        );

        return array_values(
            static::filterNull($arr)
        );
    }

    public static function str(mixed $val): array
    {
        $arr = static::flat($val);

        $arr = array_map(
            function($item) {
                return is_string($item)
                    ? Str::val($item)
                    : null;
            },
            $arr
        );

        return array_values(
            static::filterNull($arr)
        );
    }

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
