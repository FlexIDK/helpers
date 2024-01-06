<?php

namespace One23\Helpers;

use Illuminate\Support\Arr as IlluminateArr;
use Illuminate\Support\Str as IlluminateStr;
use One23\Helpers\Exceptions\Url as Exception;

class ObjectUrl
{
    protected array $components;

    /**
     * @param array{
     *     onlyHttp: ?bool,
     *     minHostLevel: ?int,
     *     acceptPort: ?bool,
     *     acceptIp: ?bool,
     *     acceptAuth: ?bool} $options
     */
    public function __construct(
        string|array|self $val,
        array $options = [],
    ) {
        if (is_string($val)) {
            $this->parse(
                $val,
                $options,
            );
        } elseif (is_array($val)) {
            $this->parse(
                $this->build($val),
                $options,
            );
        } elseif ($val instanceof self) {
            $this->components = $val->toArray();
        } else {
            throw new Exception('Invalid `value`');
        }
    }

    public function toArray(): array
    {
        return $this->components ?? [];
    }

    public function toString(): string
    {
        return $this->build();
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    protected function optionsWithDefault(array $options = []): array
    {
        return $options + [
            'onlyHttp' => $options['onlyHttp'] ?? true,
            'minHostLevel' => $options['minHostLevel'] ?? 2,
            'acceptPort' => $options['acceptPort'] ?? false,
            'acceptIp' => $options['acceptIp'] ?? false,
            'acceptAuth' => $options['acceptAuth'] ?? false,
        ];
    }

    //

    /**
     * @param array{
     *     onlyHttp: ?bool,
     *     minHostLevel: ?int,
     *     acceptPort: ?bool,
     *     acceptIp: ?bool,
     *     acceptAuth: ?bool} $options
     */
    protected function parse(
        string $url,
        array $options = [],
    ): static {
        $options = $this->optionsWithDefault($options);

        //

        $components = parse_url($url);
        if (! $components) {
            throw new Exception('Invalid URL');
        }

        if (
            ! isset($components['scheme']) ||
            ! isset($components['host'])
        ) {
            throw new Exception('Undefined scheme or host');
        }

        if (is_bool($options['acceptPort'] ?? null)) {
            if (
                $options['acceptPort']
                && ! isset($components['port'])
            ) {
                throw new Exception('Undefined port');
            }

            if (
                ! $options['acceptPort']
                && isset($components['port'])
            ) {
                throw new Exception('Port is not allowed');
            }
        }

        if (is_bool($options['acceptAuth'] ?? null)) {
            if (
                $options['acceptAuth']
                && ! isset($components['user'])
            ) {
                throw new Exception('Undefined user');
            }

            if (
                ! $options['acceptAuth']
                && isset($components['user'])
            ) {
                throw new Exception('User is not allowed');
            }
        }

        $components['scheme'] = $this->value2scheme(
            $components['scheme'],
            $options,
        );

        $components['host'] = $this->value2host(
            $components['host'],
            $options,
        );

        if (isset($components['query'])) {
            $components['query'] = $this->setQuery(
                $components['query'],
                null
            )->getQuery();
        }

        $components['path'] = $this->value2path(
            $components['path'] ?? null
        );

        //
        $this->components = $components;

        return $this;
    }

    /**
     * @param  array{hostHuman: bool, minHostLevel: int}  $options
     */
    public function build(
        ?array $components2replace = null,
        array $options = []
    ): string {
        $components = [
            ...$this->toArray(),
            ...($components2replace ?: []),
        ];

        if (
            ! isset($components['scheme']) ||
            ! isset($components['host'])
        ) {
            throw new Exception('Undefined scheme or host');
        }

        if (isset($components['query'])) {
            $this->components['query'] = Arr::undot(
                $this->query2dotArray($components['query'])
            );
        }

        //

        $port = Number::int($components['port'] ?? null, null, 1, 65535);

        return
            $this->value2scheme($components['scheme']) .
            '://' .

            (($components['user'] ?? null)
                ? $components['user'] .
                    (($components['pass'] ?? null)
                        ? ':' . $components['pass']
                        : '') . '@'
                : '') .

            $this->value2host(
                $components['host'],
                [
                    'minHostLevel' => (int)($options['minHostLevel'] ?? null),
                    'human' => (bool)($options['hostHuman'] ?? false),
                ]
            ) .

            (($port)
                ? ":{$port}"
                : '') .

            $this->value2path($components['path'] ?? null) .

            (! empty($components['query'] ?? [])
                ? '?' . $this->queryArray2queryString($components['query'])
                : '') .

            (($components['fragment'] ?? null)
                ? '#' . $components['fragment']
                : '');
    }

    // scheme

    /**
     * @param  array{onlyHttp: ?bool}  $options
     */
    protected function value2scheme(
        string $val,
        ?array $options = null,
    ): string {
        if (! $val) {
            throw new Exception('Undefined scheme');
        }

        $val = IlluminateStr::lower($val);

        if (! is_null($options['onlyHttp'] ?? null)) {
            if (
                (
                    $options['onlyHttp']
                    && ! in_array($val, ['http', 'https'])
                )
                || (
                    ! $options['onlyHttp']
                    && in_array($val, ['http', 'https'])
                )
            ) {
                throw new Exception('Invalid scheme');
            }
        }

        return $val;
    }

    /**
     * @param  array{onlyHttp: ?bool}  $options
     */
    public function setScheme(
        string $val,
        array $options = []
    ): static {
        $options = $this->optionsWithDefault($options);

        //

        $this->components['scheme'] = $this->value2scheme(
            $val,
            $options,
        );

        return $this;
    }

    public function getScheme(): string
    {
        return $this->components['scheme'];
    }

    // host

    /**
     * @param array{minHostLevel: int,
     *     acceptIp: ?bool} $options
     */
    protected function value2host(
        string $val,
        array $options = [],
    ): string {
        if (! $val) {
            throw new Exception('Undefined host');
        }

        $val = IlluminateStr::lower($val);

        $val = idn_to_ascii($val);
        if (! $val) {
            throw new Exception('Invalid IDN host');
        }

        if ($options['human'] ?? false) {
            try {
                $val = idn_to_utf8($val, 0, INTL_IDNA_VARIANT_UTS46);
            } catch (\Throwable) {
            }
        }

        // ipv4
        if (preg_match('@^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$@', $val)) {
            if (! Str::isIpV4($val)) {
                throw new Exception('Invalid IPv4 host');
            }

            if (($options['acceptIp'] ?? null) === false) {
                throw new Exception('IP is not allowed');
            }
        }
        // ipv6
        elseif (preg_match('@^\[([a-f0-9:]+)\]$@i', $val, $match)) {
            if (! Str::isIpV6($match[1])) {
                throw new Exception('Invalid IPv6 host');
            }

            if (($options['acceptIp'] ?? null) === false) {
                throw new Exception('IP is not allowed');
            }
        }
        // hostname
        elseif (((int)$options['minHostLevel'] ?? 0) > 0) {
            $host = preg_replace('/^www\./u', '', $val);
            $e = array_filter(explode('.', $host));

            if (count($e) < (int)$options['minHostLevel']) {
                throw new Exception('Host level is too low');
            }
        }

        return $val;
    }

    /**
     * @param  array{minHostLevel: int, acceptIp: ?bool}  $options
     */
    public function setHost(
        string $host,
        array $options = [],
    ): static {
        $options = $this->optionsWithDefault($options);

        $this->components['host'] = $this->value2host(
            $host,
            $options,
        );

        return $this;
    }

    public function getHost(): string
    {
        return $this->components['host'];
    }

    public function getHostHuman(): string
    {
        $host = $this->getHost();

        try {
            $host = idn_to_utf8($host, 0, INTL_IDNA_VARIANT_UTS46);
        } catch (\Throwable) {
        }

        return preg_replace('/^www\./ui', '', $host);
    }

    public function getHost1Level(): string
    {
        if ($this->isIp()) {
            return $this->getHost();
        }

        $host = $this->getHost();

        return implode(
            '.',
            array_slice(
                explode('.', $host),
                -1,
                1
            )
        );
    }

    public function getHost2level(): string
    {
        if ($this->isIp()) {
            return $this->getHost();
        }

        $host = $this->getHost();

        return implode(
            '.',
            array_slice(
                explode('.', $host),
                -2,
                2
            )
        );
    }

    public function getHostCrc(): string
    {
        return md5($this->getHostHuman());
    }

    // path

    protected function value2path(?string $val = null): string
    {
        // remover trailing slash
        return preg_replace(
            '/\/+/', '/',
            $val ?: '/'
        );
    }

    public function setPath(?string $path = null): static
    {
        $this->components['path'] = $this->value2path($path);

        return $this;
    }

    public function getPath(): string
    {
        return $this->components['path'] ?? '/';
    }

    // query

    protected function queryValue(mixed $val): string
    {
        if (is_bool($val)) {
            return '=' . ($val ? '1' : '0');
        }

        $val = Value::val($val);
        if (is_null($val)) {
            return '';
        }

        $val = Str::val($val, '', false);

        return '=' . urlencode($val);
    }

    protected function queryKeyValueArray(array $val, string $key): array
    {
        $res = [];

        $isList = array_is_list($val);
        foreach ($val as $k => $v) {
            if ($isList) {
                $k = null;
            }

            $newKey = $key . '[' . ($k ? urlencode($k) : '') . ']';

            if (is_array($v)) {
                $arr = $this->queryKeyValueArray($v, $newKey);
                array_walk(
                    $arr,
                    function($val) use (&$res) {
                        $res[] = $val;
                    }
                );

                continue;
            }

            $res[] = $newKey . $this->queryValue($v);
        }

        return $res;
    }

    protected function queryArray2queryString(array $query): string
    {
        $res = [];
        foreach ($query as $key => $val) {
            if (is_array($val)) {
                $arr = $this->queryKeyValueArray($val, urlencode((string)$key));

                array_walk(
                    $arr,
                    function($val) use (&$res) {
                        $res[] = $val;
                    }
                );
            } else {
                $res[] = urlencode($key) . $this->queryValue($val);
            }
        }

        return implode('&', $res);
    }

    protected function query2dotArray(array|string|null $qs = null): array
    {
        $res = [];
        if (is_string($qs)) {
            parse_str($qs, $arr);
            $res = Arr::dot($arr);

            $arr = explode('&', $qs);

            array_walk(
                $arr,
                function($val) use (&$res) {
                    if (! $val) {
                        return;
                    }

                    if (! str_contains($val, '=')) {
                        parse_str($val, $a);

                        $r = array_key_first(Arr::dot($a));
                        if ($r) {
                            $res[$r] = null;
                        }
                    }
                }
            );
        } elseif (is_array($qs)) {
            $res = Arr::dot($qs);
        }

        return $res;
    }

    /**
     * @param  bool|null  $append null: replace, true: append, false: prepend
     */
    public function setQuery(
        string|array|null $qs = null,
        ?bool $append = null
    ): static {
        if (empty($qs)) {
            $this->components['query'] = [];

            return $this;
        }

        //

        $queryBefore = $this->getQuery();

        $res = $this->query2dotArray($qs);

        //

        if (is_null($append)) {
            $this->components['query'] = Arr::undot($res);
        } elseif ($append) {
            $this->components['query'] = Arr::undot(
                Arr::dotMerge(
                    Arr::dot($queryBefore),
                    $res
                )
            );
        } else {
            $this->components['query'] = Arr::undot(
                Arr::dotMerge(
                    $res,
                    Arr::dot($queryBefore),
                )
            );
        }

        return $this;
    }

    public function getQuery(): array
    {
        return $this->components['query'] ?? [];
    }

    public function getQueryString(): string
    {
        return $this->queryArray2queryString(
            $this->getQuery()
        );
    }

    public function removeQuery(array|string $keys): static
    {
        $keys = IlluminateArr::wrap($keys);

        $res = $this->getQuery();

        foreach ($keys as $key) {
            IlluminateArr::forget($res, $key);
        }

        $this->components['query'] = $res;

        return $this;
    }

    // url

    public function getUrlIdn(): string
    {
        return $this->toString();
    }

    public function getUrlHuman(): string
    {
        return $this->build(
            null,
            [
                'hostHuman' => true,
            ]
        );
    }

    // auth

    public function getAuth(): ?array
    {
        $user = $this->components['user'] ?? null;
        $pass = $this->components['pass'] ?? null;

        if ($user) {
            return [$user, $pass];
        }

        return null;
    }

    public function getPort(): ?int
    {
        return $this->components['port'] ?? null;
    }

    public function hasPort(): bool
    {
        return (bool)($this->getPort());
    }

    public function isHttp(): bool
    {
        return in_array($this->getScheme(), ['http', 'https']);
    }

    public function isSchemes(array|string $schemes): bool
    {
        $schemes = IlluminateArr::wrap($schemes);

        return in_array($this->getScheme(), $schemes);
    }

    public function isIp(): bool
    {
        return Str::isIp($this->getHost());
    }

    public function isIpV6(): bool
    {
        return Str::isIpV6($this->getHost());
    }

    public function isIpV4(): bool
    {
        return Str::isIpV4($this->getHost());
    }
}
