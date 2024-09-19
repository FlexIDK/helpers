<?php

namespace One23\Helpers\Traits\ObjectUrl;

use Illuminate\Support\Arr as IlluminateArr;
use One23\Helpers\Arr;
use One23\Helpers\Str;
use One23\Helpers\Value;

trait Query
{
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

    protected function queryArray(array $val, string $key): array
    {
        $res = [];

        $isList = array_is_list($val);
        foreach ($val as $k => $v) {
            if ($isList) {
                $k = null;
            }

            $newKey = $key . '[' . (! is_null($k) ? urlencode($k) : '') . ']';

            if (is_array($v)) {
                $arr = $this->queryArray($v, $newKey);
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

    protected function query2string(array $query): string
    {
        $res = [];
        foreach ($query as $key => $val) {
            if (is_array($val)) {
                $arr = $this->queryArray($val, urlencode((string)$key));

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

    protected function queryString2dot(string $query): array
    {
        $arr = [];

        //

        parse_str($query, $arr);
        $res = Arr::dot($arr);

        //

        $arr = explode('&', $query);
        array_walk(
            $arr,
            function($val) use (&$res) {
                if (! $val) {
                    return;
                }

                if (! str_contains($val, '=')) {
                    $a = [];
                    parse_str($val, $a);

                    $r = array_key_first(Arr::dot($a));
                    if ($r) {
                        $res[$r] = null;
                    }
                }
            }
        );

        return $res;
    }

    protected function query2dot(array|string|null $query = null): array
    {
        if (empty($query)) {
            return [];
        }

        //

        $res = [];

        if (is_string($query)) {
            $res = $this->queryString2dot($query);
        } elseif (is_array($query)) {
            $res = Arr::dot($query);
        }

        return $res;
    }

    //

    /**
     * @param  bool|null  $append  null: replace, true: append, false: prepend
     */
    public function setQuery(
        string|array|null $query = null,
        ?bool $append = null
    ): static {
        $self = $this->self();

        //

        $dotQuery = $this->query2dot($query);
        if (empty($dotQuery)) {
            if (is_null($append)) {
                $self->setComponent('query', []);
            }

            return $self;
        }

        //

        $dotQueryBefore = $this->getComponent('query');

        if (is_null($append)) {
            // replace
            $self->setComponent('query', $dotQuery);
        } elseif ($append) {
            // append
            $self->setComponent('query', Arr::dotMerge(
                $dotQueryBefore,
                $dotQuery
            ));
        } else {
            // prepend
            $self->setComponent('query', Arr::dotMerge(
                $dotQuery,
                Arr::dot($dotQueryBefore),
            ));
        }

        return $self;
    }

    public function append2Query(string|array $query): static
    {
        return $this->setQuery($query, true);
    }

    public function prepend2Query(string|array $query): static
    {
        return $this->setQuery($query, false);
    }

    public function replaceQuery(string|array $query): static
    {
        return $this->setQuery($query, null);
    }

    public function getQuery(?string $key = null, $default = null): mixed
    {
        if (! is_null($key)) {
            return $this->getComponent('query')[$key] ?? $default;
        }

        return IlluminateArr::undot(
            $this->getComponent('query')
        );
    }

    /**
     * @param  string  $direction  `asc` or `desc`
     */
    public function sortQueryKeys(
        string $direction = 'asc'
    ): static {
        $direction = match ($direction) {
            'asc', 'desc' => $direction,
            default => 'asc',
        };

        $q = $this->getComponent('query');

        if ($direction === 'asc') {
            ksort($q, SORT_STRING);
        } else {
            krsort($q, SORT_STRING);
        }

        //

        $self = $this->self();
        $self->setComponent('query', $q);

        return $self;
    }

    public function getQueryString(): string
    {
        return $this->query2string(
            $this->getQuery()
        );
    }

    public function removeQueryKeys(array|string $keys): static
    {
        $query = $this->getComponent('query');
        if (empty($query)) {
            return $this;
        }

        //

        $keys = IlluminateArr::wrap($keys);
        foreach ($keys as $key) {
            if (! is_string($key)) {
                continue;
            }

            if (array_key_exists($key, $query)) {
                unset($query[$key]);

                continue;
            }

            foreach ($query as $queryKey => $v) {
                if (str_starts_with($queryKey, $key . '.')) {
                    unset($query[$queryKey]);
                }
            }
        }

        //

        $self = $this->self();
        $self->setComponent('query', $query);

        return $self;
    }

    /**
     * @deprecated
     *
     * @param  string[]|string|null  $keys
     */
    public function removeQuery(array|string|null $keys = null): static
    {
        if (is_null($keys)) {
            $self = $this->self();
            $self->setComponent('query', []);

            return $self;
        }

        return $this->removeQueryKeys($keys);
    }
}
