<?php

namespace One23\Helpers;

use Egulias\EmailValidator;

class Email
{
    public static function isValid(mixed $val): bool
    {
        $str = Str::trim($val);
        if (empty($str)) {
            return false;
        }

        if (str_contains($val, '?')) {
            return false;
        }

        return static::val($val) !== null;
    }

    public static function val(mixed $val): ?string
    {
        $str = Str::trim($val);
        if (! $str) {
            return null;
        }

        $str = mb_convert_case($str, MB_CASE_LOWER, 'UTF-8');

        // qs is not supported
        if (str_contains($str, '?')) {
            $str = explode('?', $str, 2)[0];
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
        $validator = new EmailValidator\EmailValidator;
        $res = $validator->isValid(
            $str,
            new EmailValidator\Validation\RFCValidation
        );

        return $res
            ? $str
            : null;
    }

    public static function mask(
        mixed $val,
        string $mask = '*',
        int $length = 3
    ): ?string {
        $email = static::val($val);
        if (empty($email)) {
            return null;
        }

        $parts = explode('@', $email, 2);

        $name = $parts[0] ?: '';
        $domain = $parts[1] ?? null;

        //

        $nameMask = Str::mask($name, $mask, $length);

        try {
            $url = Url::fromUri('//' . $domain . '/');
            $host = $url->getHostHuman();

            $e = explode('.', $host);

            $d = implode('.', array_slice(
                $e,
                0,
                -1
            ));

            $z = implode('.', array_slice(
                $e,
                -1,
                1,
            ));

            $domainMask = Str::mask($d, $mask, $length);
            $zoneMask = Str::mask($z, $mask, $length);
        } catch (\Throwable $e) {
            $domainMask = $zoneMask = Str::mask('', $mask, $length);
        }

        return $nameMask . '@' . $domainMask . '.' . $zoneMask;
    }
}
