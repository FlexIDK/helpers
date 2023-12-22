<?php

namespace One23\Helpers;

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
        mixed $val,
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
        mixed $val,
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
    public static function url(
        mixed $val,
        array $options = [],
    ): ObjectUrl {
        return new ObjectUrl(
            $val,
            $options
        );
    }

    protected static function urlUnchecked(
        mixed $val,
    ): ObjectUrl {
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
        mixed $val
    ): bool {
        return static::urlUnchecked($val)
            ->isIp();
    }

    public static function isIpV4(
        mixed $val
    ): bool {
        return static::urlUnchecked($val)
            ->isIpV4();
    }

    public static function isIpV6(
        mixed $val
    ): bool {
        return static::urlUnchecked($val)
            ->isIpV6();
    }

    public static function isHttp(
        mixed $val
    ): bool {
        return static::urlUnchecked($val)
            ->isHttp();
    }

    public static function host(
        mixed $val
    ): string {
        return static::urlUnchecked($val)
            ->getHost();
    }

    public static function hostHuman(
        mixed $val
    ): string {
        return static::urlUnchecked($val)
            ->getHostHuman();
    }

    public static function host2level(
        mixed $val
    ): string {
        return static::urlUnchecked($val)
            ->getHost2level();
    }
}
