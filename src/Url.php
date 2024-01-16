<?php

namespace One23\Helpers;

class Url
{
    protected static $defaultOptions = [
        'allowWildcard' => false,
        'onlyHttp' => null,
        'acceptAuth' => null,
        'minHostLevel' => 2,
        'maxHostLevel' => 127,
        'maxHostLength' => 253,
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
     *      minHostLevel: int,
     *      maxHostLevel: int,
     *      maxHostLength: int,
     *      acceptPort: ?bool,
     *      acceptIp: ?bool,
     *      acceptAuth: ?bool
     * } $options
     */
    public static function parse(
        string|array|ObjectUrl|null $val = null,
        array $options = [],
    ): ?array {
        if (empty($val)) {
            return null;
        }

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
     *      minHostLevel: int,
     *      maxHostLevel: int,
     *      maxHostLength: int,
     *      acceptPort: ?bool,
     *      acceptIp: ?bool,
     *      acceptAuth: ?bool
     * } $options
     */
    public static function build(
        string|array|ObjectUrl|null $val = null,
        array $components2replace = [],
        array $options = [],
    ): ?string {
        if (empty($val)) {
            return null;
        }

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
     *      minHostLevel: int,
     *      maxHostLevel: int,
     *      maxHostLength: int,
     *      acceptPort: ?bool,
     *      acceptIp: ?bool,
     *      acceptAuth: ?bool
     * } $options
     */
    public static function object(
        string|array|ObjectUrl|null $val = null,
        array $options = [],
    ): ObjectUrl {
        return new ObjectUrl(
            $val,
            $options
        );
    }

    protected static function defaultObject(
        string|array|ObjectUrl|null $val = null,
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
        string|array|ObjectUrl|null $val = null,
        array $options = [],
    ): bool {
        try {
            $obj = static::defaultObject($val, $options);
            if (! $obj) {
                return false;
            }

            return $obj->isIp();
        } catch (\Throwable) {
            return false;
        }
    }

    public static function isIpV4(
        string|array|ObjectUrl|null $val = null,
        array $options = [],
    ): bool {
        try {
            $obj = static::defaultObject($val, $options);
            if (! $obj) {
                return false;
            }

            return $obj->isIpV4();
        } catch (\Throwable) {
            return false;
        }
    }

    public static function isIpV6(
        string|array|ObjectUrl|null $val = null,
        array $options = [],
    ): bool {
        try {
            $obj = static::defaultObject($val, $options);
            if (! $obj) {
                return false;
            }

            return $obj->isIpV6();
        } catch (\Throwable) {
            return false;
        }
    }

    public static function isHttp(
        string|array|ObjectUrl|null $val = null,
        array $options = [],
    ): bool {
        if (empty($val)) {
            return false;
        }

        try {
            $obj = static::defaultObject($val, $options);
            if (! $obj) {
                return false;
            }

            return $obj->isHttp();
        } catch (\Throwable) {
            return false;
        }
    }

    public static function host(
        string|array|ObjectUrl|null $val = null,
        array $options = [],
    ): ?string {
        try {
            return static::defaultObject($val, $options)
                ?->getHost();
        } catch (\Throwable) {
            return null;
        }
    }

    public static function hostHuman(
        string|array|ObjectUrl|null $val = null,
        array $options = [],
    ): ?string {
        try {
            return static::defaultObject($val, $options)
                ?->getHostHuman();
        } catch (\Throwable) {
            return null;
        }
    }

    public static function host2level(
        string|array|ObjectUrl|null $val = null,
        array $options = [],
    ): ?string {
        try {
            return static::defaultObject($val, $options)
                ?->getHost2level();
        } catch (\Throwable) {
            return null;
        }
    }
}
