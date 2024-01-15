<?php

namespace One23\Helpers;

class Url
{
    protected static $defaultOptions = [
        'allowWildcard' => false,
        'onlyHttp' => null,
        'acceptAuth' => null,
        'minHostLevel' => 2,
        'acceptIp' => null,
        'acceptPort' => null,
    ];

    protected static function options(
        array $options = []
    ): array {
        return Options::merge(
            static::$defaultOptions,
            $options
        );
    }

    /**
     * @param array{
     *      defaultScheme: ?string,
     *      allowWildcard: ?bool,
     *      onlyHttp: ?bool,
     *      minHostLevel: ?int,
     *      acceptPort: ?bool,
     *      acceptIp: ?bool,
     *      acceptAuth: ?bool
     * } $options
     */
    public static function parse(
        string|array|ObjectUrl $val,
        array $options = [],
    ): ?array {
        try {
            return (new ObjectUrl(
                $val,
                static::options($options)
            ))
                ->toArray();
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @param array{
     *      defaultScheme: ?string,
     *      allowWildcard: ?bool,
     *      onlyHttp: ?bool,
     *      minHostLevel: ?int,
     *      acceptPort: ?bool,
     *      acceptIp: ?bool,
     *      acceptAuth: ?bool
     * } $options
     */
    public static function build(
        string|array|ObjectUrl $val,
        array $components2replace = [],
        array $options = [],
    ): ?string {
        try {
            $options = static::options($options);

            return (new ObjectUrl($val, $options))
                ->build($components2replace, $options);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @param array{
     *      defaultScheme: ?string,
     *      allowWildcard: ?bool,
     *      onlyHttp: ?bool,
     *      minHostLevel: ?int,
     *      acceptPort: ?bool,
     *      acceptIp: ?bool,
     *      acceptAuth: ?bool
     * } $options
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

    protected static function defaultObject(
        string|array|ObjectUrl $val,
        array $options = [],
    ): ?ObjectUrl {
        if (empty($val)) {
            return null;
        }

        return new ObjectUrl(
            $val,
            static::options($options)
        );
    }

    public static function isIp(
        string|array|ObjectUrl $val,
        array $options = [],
    ): bool {
        try {
            return (bool)static::defaultObject($val, $options)
                ?->isIp();
        } catch (\Throwable) {
            return false;
        }
    }

    public static function isIpV4(
        string|array|ObjectUrl $val,
        array $options = [],
    ): bool {
        try {
            return (bool)static::defaultObject($val, $options)
                ?->isIpV4();
        } catch (\Throwable) {
            return false;
        }
    }

    public static function isIpV6(
        string|array|ObjectUrl $val,
        array $options = [],
    ): bool {
        try {
            return (bool)static::defaultObject($val, $options)
                ?->isIpV6();
        } catch (\Throwable) {
            return false;
        }
    }

    public static function isHttp(
        string|array|ObjectUrl $val,
        array $options = [],
    ): bool {
        if (empty($val)) {
            return false;
        }

        try {
            return static::defaultObject($val, $options)
                ?->isHttp();
        } catch (\Throwable) {
            return false;
        }
    }

    public static function host(
        string|array|ObjectUrl $val,
        array $options = [],
    ): ?string {
        return static::defaultObject($val, $options)
            ?->getHost();
    }

    public static function hostHuman(
        string|array|ObjectUrl $val,
        array $options = [],
    ): ?string {
        return static::defaultObject($val, $options)
            ?->getHostHuman();
    }

    public static function host2level(
        string|array|ObjectUrl $val,
        array $options = [],
    ): ?string {
        return static::defaultObject($val, $options)
            ?->getHost2level();
    }
}
