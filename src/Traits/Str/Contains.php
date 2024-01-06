<?php

namespace One23\Helpers\Traits\Str;

use Illuminate\Support\Arr as IlluminateArr;
use Illuminate\Support\Str as IlluminateStr;
use One23\Helpers\Arr;
use One23\Helpers\Str;

/**
 * @method static val
 */
trait Contains
{
    protected static function isWordSeparator(string $val): bool
    {
        if (
            $val === '' ||
            $val === ' '
        ) {
            return true;
        }

        if (preg_match('/[a-z0-9]/ui', $val)) {
            return false;
        }

        return true;
    }

    protected static function haystackAndNeedles(mixed $haystack, array|string $needles, bool $caseSensitive = true): array
    {
        $needles = IlluminateArr::wrap($needles);
        $needles = array_map(function($needle) use ($caseSensitive) {
            $val = static::val($needle);

            return $val
                ? ($caseSensitive ? $val : IlluminateStr::lower($val))
                : null;
        }, $needles);

        $haystack = static::val($haystack);
        $haystack = $caseSensitive
            ? $haystack
            : ($haystack ? IlluminateStr::lower($haystack) : null);

        return [
            $haystack,
            Arr::filterNull($needles),
        ];
    }

    public static function with(
        mixed $haystack,
        array|string $needles,
        bool $caseSensitive = true,
        ?string &$match = null
    ): bool {
        $match = null;

        [$haystack, $needles] = static::haystackAndNeedles($haystack, $needles, $caseSensitive);
        if (! $haystack) {
            return false;
        }

        foreach ($needles as $needle) {
            if (! $needle) {
                continue;
            }

            $i = 0;
            while (true) {
                $pos = mb_strpos($haystack, $needle, $i);
                if ($pos === false) {
                    break;
                }

                $before = mb_substr(
                    mb_substr($haystack, 0, $pos),
                    -1, 1
                );
                $after = mb_substr($haystack, $pos + mb_strlen($needle), 1);

                if (
                    ! Str::isWordSeparator($before)
                    || ! Str::isWordSeparator($after)
                ) {
                    $i = $pos + 1;

                    continue;
                }

                $match = $needle;

                return true;
            }
        }

        return false;
    }

    public static function contains(
        mixed $haystack,
        array|string $needles,
        bool $caseSensitive = true,
        ?string &$match = null
    ): bool {
        $match = null;

        [$haystack, $needles] = static::haystackAndNeedles($haystack, $needles, $caseSensitive);
        if (! $haystack) {
            return false;
        }

        foreach ($needles as $needle) {
            if (! $needle) {
                continue;
            }

            if (str_contains($haystack, $needle)) {
                $match = $needle;

                return true;
            }
        }

        return false;
    }

    public static function startWith(
        mixed $haystack,
        array|string $needles,
        bool $caseSensitive = true,
        ?string &$match = null
    ): bool {
        $match = null;

        [$haystack, $needles] = static::haystackAndNeedles($haystack, $needles, $caseSensitive);
        if (! $haystack) {
            return false;
        }

        foreach ($needles as $needle) {
            if (! $needle) {
                continue;
            }

            if (str_starts_with($haystack, $needle)) {
                $match = $needle;

                return true;
            }
        }

        return false;
    }

    public static function endWith(
        mixed $haystack,
        array|string $needles,
        bool $caseSensitive = true,
        ?string &$match = null
    ): bool {
        $match = null;

        [$haystack, $needles] = static::haystackAndNeedles($haystack, $needles, $caseSensitive);
        if (! $haystack) {
            return false;
        }

        foreach ($needles as $needle) {
            if (! $needle) {
                continue;
            }

            if (str_ends_with($haystack, $needle)) {
                $match = $needle;

                return true;
            }
        }

        return false;
    }
}
