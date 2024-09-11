<?php

namespace One23\Helpers;

use Illuminate\Contracts\Support\Arrayable;
use One23\Helpers\Exceptions\ObjectUrl as Exception;

class ObjectUrl implements \Stringable, Arrayable
{
    use Traits\ObjectUrl\Auth;
    use Traits\ObjectUrl\Binding;
    use Traits\ObjectUrl\Components;
    use Traits\ObjectUrl\Fragment;
    use Traits\ObjectUrl\Host;
    use Traits\ObjectUrl\Mutable;
    use Traits\ObjectUrl\Path;
    use Traits\ObjectUrl\Port;
    use Traits\ObjectUrl\Query;
    use Traits\ObjectUrl\Scheme;

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
            $this->setComponent(
                $val->getComponent()
            );
        } else {
            throw new Exception('Invalid `value`', Exception::INVALID_VALUE);
        }
    }

    /**
     * @return array{
     *      scheme: string,
     *      host: string,
     *      port: integer,
     *      user: string,
     *      pass: string,
     *      path: string,
     *      query: array,
     *      query_string: string,
     *      fragment: string,
     * }
     */
    public function toArray(): array
    {
        return [
            ...($this->getComponent()),

            'query' => $this->getQuery(),
            'query_string' => $this->getQueryString(),
        ];
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

        $parseComponents = parse_url($url);
        if (empty($parseComponents)) {
            throw new Exception('Invalid URL', Exception::INVALID_URL);
        }

        $parseComponents['scheme'] = $this->value2scheme(
            ($parseComponents['scheme'] ?? null),
            $options,
        );

        $parseComponents = [
            ...$parseComponents,
            ...($this->value2auth(
                (($parseComponents['user'] ?? null)
                    ? rawurldecode((string)$parseComponents['user'])
                    : null),
                (($parseComponents['pass'] ?? null)
                    ? rawurldecode((string)$parseComponents['pass'])
                    : null),
                $options,
            ) ?: []),
        ];

        $parseComponents['host'] = $this->value2host(
            ($parseComponents['host'] ?? null),
            $options,
        );

        $parseComponents['port'] = $this->value2port(
            ($parseComponents['port'] ?? null),
            $options,
        );

        $parseComponents['path'] = $this->value2path(
            $parseComponents['path'] ?? null
        );

        if (isset($parseComponents['query'])) {
            $parseComponents['query'] = $this->query2dot(
                $parseComponents['query']
            );
        }

        $parseComponents['fragment'] = $this->value2fragment(
            $parseComponents['fragment'] ?? null
        );

        //

        $this->setComponent($parseComponents);

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
            ...$this->getComponent(),

            ...($components2replace ?: []),
            ...(isset($components2replace['query'])
                ? [
                    'query' => $this->query2dot($components2replace['query']),
                ]
                : []
            ),
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

    public function getUri(
        ?array $components2replace = null,
    ): string {
        $components = [
            ...$this->getComponent(),

            ...($components2replace ?: []),
        ];

        $components['path'] = $this->value2path(
            $components['path'] ?? null
        );

        if (
            ! empty($components['query'] ?? []) &&
            (
                is_string($components['query']) ||
                is_array($components['query'])
            )
        ) {
            $components['query'] = $this->query2dot($components['query']);
        }

        $components['fragment'] = $this->value2fragment(
            $components['fragment'] ?? null
        );

        //

        $fragment = $this->fragment2build($components);

        return
            $this->path2build($components) .

            (! empty($components['query'] ?? [])
                ? '?' . $this->query2string($components['query'])
                : '') .

            ($fragment ? ('#' . $fragment) : '');
    }
}
