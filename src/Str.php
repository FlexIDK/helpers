<?php

namespace One23\Helpers;

use Egulias\EmailValidator;
use Illuminate\Support\Arr as IlluminateArr;
use Illuminate\Support\Str as IlluminateStr;
use One23\Helpers\Exceptions\Str as Exception;

class Str
{
    public static function md5(mixed $val, int $length = 32): string
    {
        $length = Number::int($length, null, 1, 32);
        if (! $length) {
            throw new Exception('Invalid length');
        }

        $val = static::val($val);
        if (is_null($val)) {
            throw new Exception('Value is null');
        }

        return mb_substr(
            md5($val ?: ''),
            0, $length
        );
    }

    public static function isCrc(mixed $val, $length = 32): bool
    {
        $val = static::val($val);

        if (! is_string($val)) {
            return false;
        }

        if (mb_strlen($val) !== $length) {
            return false;
        }

        if (preg_match('@^[a-f0-9]+$@ui', $val)) {
            return true;
        }

        return false;
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
        $haystack = $caseSensitive ? $haystack : IlluminateStr::lower($haystack);

        return [
            $haystack,
            $needles,
        ];
    }

    public static function contains(mixed $haystack, array|string $needles, bool $caseSensitive = true): bool
    {
        [$haystack, $needles] = static::haystackAndNeedles($haystack, $needles, $caseSensitive);
        if (! $haystack) {
            return false;
        }

        foreach ($needles as $needle) {
            if (! $needle) {
                continue;
            }

            if (str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    public static function startWith(mixed $haystack, array|string $needles, bool $caseSensitive = true): bool
    {
        [$haystack, $needles] = static::haystackAndNeedles($haystack, $needles, $caseSensitive);
        if (! $haystack) {
            return false;
        }

        foreach ($needles as $needle) {
            if (! $needle) {
                continue;
            }

            if (str_starts_with($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    public static function endWith(mixed $haystack, array|string $needles, bool $caseSensitive = true): bool
    {
        [$haystack, $needles] = static::haystackAndNeedles($haystack, $needles, $caseSensitive);
        if (! $haystack) {
            return false;
        }

        foreach ($needles as $needle) {
            if (! $needle) {
                continue;
            }

            if (str_ends_with($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    public static function val(mixed $val, ?string $default = null, bool $trim = true): ?string
    {
        $val = Value::val($val);

        if (
            ! is_string($val)
            && ! is_numeric($val)
        ) {
            return $default;
        }

        $val = (string)$val;
        if ($trim) {
            $val = trim($val);
        }

        if ($val === '') {
            return $default;
        }

        return $val;
    }

    public static function orNull(mixed $val): ?string
    {
        return static::val($val, null);
    }

    public static function orBlank(mixed $val): string
    {
        return static::val($val, '');
    }

    public static function trim(mixed $val): ?string
    {
        $str = static::val($val);
        if ($str === null) {
            return null;
        }

        $str = preg_replace('@[\t\n\r\s\v]+@u', ' ', $str);
        $str = preg_replace('@\s+@uim', ' ', $str);

        return trim($str);
    }

    public static function tag(mixed $val): ?string
    {
        $str = static::trim($val);
        if (! $str) {
            return null;
        }

        return mb_convert_case($str, MB_CASE_LOWER, 'UTF-8');
    }

    public static function email(mixed $val): ?string
    {
        $str = static::trim($val);
        if (! $str) {
            return null;
        }

        $str = mb_convert_case($str, MB_CASE_LOWER, 'UTF-8');

        // qs is not supported
        if (str_contains($str, '?')) {
            return null;
        }

        // check domain
        $domain = explode('@', $str, 2)[1] ?? null;
        if (
            ! $domain ||
            ! str_contains($domain, '.')
        ) {
            return null;
        }

        // check email
        $validator = new EmailValidator\EmailValidator();
        $res = $validator->isValid(
            $str,
            new EmailValidator\Validation\RFCValidation()
        );

        return $res
            ? $str
            : null;
    }

    public static function isEmail(mixed $val): bool
    {
        return static::email($val) !== null;
    }

    public static function isIp(mixed $val): bool
    {
        if (
            static::isIpV4($val) ||
            static::isIpV6($val)
        ) {
            return true;
        }

        return false;
    }

    public static function isIpV4(mixed $val): bool
    {
        $str = static::trim($val);
        if (! $str) {
            return false;
        }

        // check regexp ipv4
        if (! preg_match('/^(\d{1,3}\.){3}\d{1,3}$/', $str)) {
            return false;
        }

        // check range
        $parts = explode('.', $str);
        foreach ($parts as $part) {
            if (
                $part < 0 ||
                $part > 255
            ) {
                return false;
            }
        }

        return filter_var($str, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
    }

    public static function isIpV6(mixed $val): bool
    {
        $str = static::trim($val);
        if (! $str) {
            return false;
        }

        // check brackets
        if (
            str_starts_with($str, '[') &&
            str_ends_with($str, ']')
        ) {
            $str = trim($str, '[]');
        }

        // check regexp ipv6
        if (! preg_match('/^([a-f0-9:]+)$/ui', $str)) {
            return false;
        }

        return filter_var($str, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;
    }
}
