<?php

namespace One23\Helpers;

use One23\Helpers\Exceptions\Str as Exception;

class Str
{
    use Traits\First;
    use Traits\Last;
    use Traits\Str\Contains;

    public static function hasEntityCharters(string $str): bool
    {
        if (preg_match('/&([a-z0-9]+|#[0-9]{1,6}|#x[0-9a-f]{1,6});/i', $str)) {
            return true;
        }

        return false;
    }

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
        return Email::val($val);
    }

    public static function isEmail(mixed $val): bool
    {
        return Email::isValid($val);
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

    public static function mask(
        mixed $val,
        string $mask = '*',
        int $length = 3
    ): string {
        $string = static::val($val);
        $length = Integer::get($length, 1, 1);

        if (empty($string)) {
            return str_repeat($mask, $length);
        }

        $first = mb_substr($string, 0, 1, 'UTF-8');
        $last = mb_substr($string, -1, 1, 'UTF-8');
        $l = mb_strlen($string, 'UTF-8');

        if ($l <= ($length + 2)) {
            $length = max(($l - 2), 1);
        }

        return $first .
            str_repeat($mask, $length) .
            $last;
    }
}
