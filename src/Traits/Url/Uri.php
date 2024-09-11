<?php

namespace One23\Helpers\Traits\Url;

use One23\Helpers\ObjectUrl;

trait Uri
{
    protected static function _uriUrlOptions()
    {
        return [
            ...self::$_defaultOptions,
            'minHostLevel' => 1,
        ];
    }

    protected static function _uriObjectUrl(
        array $options = [],
    ): ObjectUrl {
        if ($options['url'] ?? null) {
            $objectUrl = static::object(
                $options['url'],
                self::_uriUrlOptions()
            );
        } else {
            $objectUrl = static::object(
                'https://localhost/',
                self::_uriUrlOptions()
            );

            $options = $options + [
                'scheme' => 'https',
                'host' => 'localhost',
                'baseHref' => '/',
            ];
        }

        if ($options['scheme'] ?? null) {
            $objectUrl->setScheme($options['scheme']);
        }

        if ($options['baseHref'] ?? null) {
            $objectUrl->setPath($options['baseHref']);
        }

        if ($options['host'] ?? null) {
            $objectUrl->setHost($options['host']);
        }

        return $objectUrl;
    }

    /**
     * @param  array{url: ?string, scheme: ?string, host: ?string, baseHref: ?string}  $options
     */
    public static function fromUri(
        ?string $uri = null,
        array $options = []
    ): ?ObjectUrl {
        if (is_null($uri)) {
            return null;
        }

        $objectUrl = self::_uriObjectUrl($options);

        // is url
        if (preg_match('/^[a-z0-9-+.]+:\/\//', $uri)) {
            return static::object($uri, self::_uriUrlOptions());
        }

        // without scheme
        if (str_starts_with($uri, '//')) {
            return static::object(
                ($objectUrl->getScheme() . ':' .
                    $uri),
                self::_uriUrlOptions()
            );
        }

        // absolute
        if (str_starts_with($uri, '/')) {
            return static::object(
                ($objectUrl->getScheme() . '://' .
                    $objectUrl->getHost() .
                    $uri),
                self::_uriUrlOptions()
            );
        }

        // relative
        return static::object(
            ($objectUrl->getScheme() . '://' .
                $objectUrl->getHost() .
                $objectUrl->getPath() .
                $uri),
            self::_uriUrlOptions()
        );
    }

    /**
     * @param  array{url: ?string, scheme: ?string, host: ?string, baseHref: ?string}  $options
     */
    public static function uri(
        ?string $uri = null,
        array $options = [],
        string $default = '#',
    ): string {
        $objectUrl = static::fromUri($uri, $options);
        if (! $objectUrl) {
            return $default;
        }

        return $objectUrl->getUri();
    }
}
