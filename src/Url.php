<?php

namespace One23\Helpers;

use Illuminate\Support\Str as IlluminateStr;

class Url
{
    /**
     * @param array{
     *     onlyHttp: ?bool,
     *     minHostLevel: ?int,
     *     acceptPort: ?bool,
     *     acceptIp: ?bool,
     *     acceptAuth: ?bool} $options
     */
    public static function parse(
        string|array|ObjectUrl $val,
        array $options = [],
    ): array {
        return (new ObjectUrl($val, $options))
            ->toArray();
    }

    /**
     * @param array{
     *     onlyHttp: ?bool,
     *     minHostLevel: ?int,
     *     acceptPort: ?bool,
     *     acceptIp: ?bool,
     *     acceptAuth: ?bool} $options
     */
    public static function build(
        string|array|ObjectUrl $val,
        array $components2replace = [],
        array $options = [],
    ): string {
        return (new ObjectUrl($val, $options))
            ->build($components2replace, $options);
    }

    /**
     * @param array{
     *     onlyHttp: ?bool,
     *     minHostLevel: ?int,
     *     acceptPort: ?bool,
     *     acceptIp: ?bool,
     *     acceptAuth: ?bool} $options
     */
    public static function object(
        string|array|ObjectUrl $val,
        array $options = [],
    ): ObjectUrl {
        return new ObjectUrl(
            $val,
            $options
        );
    }

    protected static function urlUnchecked(
        string|array|ObjectUrl $val,
    ): ?ObjectUrl {
        if (empty($val)) {
            return null;
        }

        return new ObjectUrl(
            $val,
            [
                'acceptIp' => null,
                'acceptPort' => null,
                'acceptAuth' => null,
                'onlyHttp' => null,
            ]
        );
    }

    public static function isIp(
        string|array|ObjectUrl $val
    ): bool {
        try {
            return (bool)static::urlUnchecked($val)
                ?->isIp();
        } catch (\Throwable) {
            return false;
        }
    }

    public static function isIpV4(
        string|array|ObjectUrl $val
    ): bool {
        try {
            return (bool)static::urlUnchecked($val)
                ?->isIpV4();
        } catch (\Throwable) {
            return false;
        }
    }

    public static function isIpV6(
        string|array|ObjectUrl $val
    ): bool {
        try {
            return (bool)static::urlUnchecked($val)
                ?->isIpV6();
        } catch (\Throwable) {
            return false;
        }
    }

    public static function isHttp(
        string|array|ObjectUrl $val
    ): bool {
        if (empty($val)) {
            return false;
        }

        if (
            is_string($val) &&
            ! preg_match('@^https?://@ui', $val)
        ) {
            return false;
        }

        if (
            is_array($val) &&
            is_string($val['scheme'] ?? null) &&
            ! in_array(
                IlluminateStr::lower($val['scheme']),
                ['http', 'https'],
                true
            )
        ) {
            return false;
        }

        try {
            return static::urlUnchecked($val)
                ?->isHttp();
        } catch (\Throwable) {
            return false;
        }
    }

    public static function host(
        string|array|ObjectUrl $val
    ): string {
        return static::urlUnchecked($val)
            ?->getHost();
    }

    public static function hostHuman(
        string|array|ObjectUrl $val
    ): string {
        return static::urlUnchecked($val)
            ?->getHostHuman();
    }

    public static function host2level(
        string|array|ObjectUrl $val
    ): string {
        return static::urlUnchecked($val)
            ?->getHost2level();
    }
}
