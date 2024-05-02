<?php

namespace One23\Helpers;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr as IlluminateArr;
use One23\Helpers\Exceptions\Url as Exception;

class ObjectUrl implements \Stringable, Arrayable
{
    use Traits\ObjectUrl\Auth;
    use Traits\ObjectUrl\Fragment;
    use Traits\ObjectUrl\Host;
    use Traits\ObjectUrl\Path;
    use Traits\ObjectUrl\Port;
    use Traits\ObjectUrl\Scheme;

    protected array $components;

    protected array $options;

    /**
     * @param array{
     *     defaultScheme: ?string,
     *     allowWildcard: ?bool,
     *     onlyHttp: ?bool,
     *     minHostLevel: int,
     *     maxHostLevel: int,
     *     maxHostLength: int,
     *     acceptPort: ?bool,
     *     acceptIp: ?bool,
     *     acceptAuth: ?bool
     * } $options
     */
    public function __construct(
        string|array|ObjectUrl|null $val = null,
        array $options = [],
    ) {
        $this->options = $this->getOptions($options);

        //

        if (empty($val)) {
            throw new Exception('Empty `value`', Exception::INVALID_VALUE);
        }

        if (is_string($val)) {
            $this->parse($val);
        } elseif (is_array($val)) {
            $this->parse(
                $this->build($val)
            );
        } elseif ($val instanceof ObjectUrl) {
            $this->components = $val->toArray();
        } else {
            throw new Exception('Invalid `value`', Exception::INVALID_VALUE);
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

    protected function getOptions(array $options = []): array
    {
        $allowedOptions = [
            'defaultScheme' => [
                'nullable' => true,
                'type' => 'string',
                'default' => null,
            ],
            'allowWildcard' => [
                'nullable' => false,
                'type' => 'bool',
                'default' => false,
            ],
            'onlyHttp' => [
                'nullable' => true,
                'type' => 'bool',
                'default' => true,
            ],
            'minHostLevel' => [
                'nullable' => false,
                'type' => 'int',
                'min' => 1,
                'max' => 127,
                'default' => 2,
            ],
            'maxHostLevel' => [
                'nullable' => false,
                'type' => 'int',
                'min' => 1,
                'max' => 127,
                'default' => 127,
            ],
            'maxHostLength' => [
                'nullable' => false,
                'type' => 'int',
                'min' => 1,
                'max' => 255,
                'default' => 253,
            ],
            'acceptPort' => [
                'nullable' => true,
                'type' => 'bool',
                'default' => false,
            ],
            'acceptIp' => [
                'nullable' => true,
                'type' => 'bool',
                'default' => false,
            ],
            'acceptAuth' => [
                'nullable' => true,
                'type' => 'bool',
                'default' => false,
            ],
            'hostHuman' => [
                'nullable' => true,
                'type' => 'bool',
                'default' => false,
            ],
        ];

        return
            [
                ...($this->options ?? []),
                ...Options::all($options, $allowedOptions),
            ] +
            Options::default($allowedOptions);
    }

    //

    protected function parse(
        string $url
    ): static {
        $options = $this->getOptions();

        //

        $components = parse_url($url);
        if (empty($components)) {
            throw new Exception('Invalid URL', Exception::INVALID_URL);
        }

        $components['scheme'] = $this->value2scheme(
            ($components['scheme'] ?? null),
            $options,
        );

        $components = [
            ...$components,
            ...($this->value2auth(
                ($components['user'] ?? null),
                ($components['pass'] ?? null),
                $options,
            ) ?: []),
        ];

        $components['host'] = $this->value2host(
            ($components['host'] ?? null),
            $options,
        );

        $components['port'] = $this->value2port(
            ($components['port'] ?? null),
            $options,
        );

        $components['path'] = $this->value2path(
            $components['path'] ?? null
        );

        if (isset($components['query'])) {
            $components['query'] = $this->setQuery(
                $components['query'],
                null
            )->getQuery();
        }

        $components['fragment'] = $this->value2fragment(
            $components['fragment'] ?? null
        );

        //

        $this->components = $components;

        return $this;
    }

    /**
     * @param  array{
     *      hostHuman: bool,
     *      minHostLevel: int,
     *      maxHostLevel: int,
     *      maxHostLength: int,
     * }  $options
     */
    public function build(
        ?array $components2replace = null,
        array $options = []
    ): string {
        $components = [
            ...$this->toArray(),
            ...($components2replace ?: []),
        ];

        $components['scheme'] = $this->value2scheme(
            ($components['scheme'] ?? null),
            $options
        );

        $components = [
            ...$components,
            ...($this->value2auth(
                ($components['user'] ?? null),
                ($components['pass'] ?? null),
                $options
            ) ?: []),
        ];

        $components['host'] = $this->value2host(
            ($components['host'] ?? null),
            $options
        );

        $components['port'] = $this->value2port(
            $components['port'] ?? null,
            $options
        );

        //

        return
            $this->scheme2build($components) .

            $this->auth2build($components) .

            $this->host2build($components) .

            $this->port2build($components) .

            $this->getUri($components);
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
     * @param  bool|null  $append  null: replace, true: append, false: prepend
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

    /**
     * @param  array{path: string|null, query: string|array, fragment: string|null}  $options
     */
    public function getUri(?array $components2replace = null): string
    {
        $components = [
            ...$this->toArray(),
            ...($components2replace ?: []),
        ];

        $components['path'] = $this->value2path(
            $components['path'] ?? null
        );

        if (! empty($components['query'] ?? [])) {
            if (is_string($components['query'])) {
                $components['query'] = $this->query2dotArray($components['query']);
            }

            if (! is_array($components['query'])) {
                throw new Exception('Invalid `query` type', Exception::INVALID_URL_QUERY);
            }

            $components['query'] = Arr::undot(
                $this->query2dotArray($components['query'])
            );
        }

        $components['fragment'] = $this->value2fragment(
            $components['fragment'] ?? null
        );

        //

        return
            $this->path2build($components) .

            (! empty($components['query'] ?? [])
                ? '?' . $this->queryArray2queryString($components['query'])
                : '') .

            $this->fragment2build($components);
    }
}
