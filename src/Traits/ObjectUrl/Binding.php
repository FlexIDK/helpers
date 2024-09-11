<?php

namespace One23\Helpers\Traits\ObjectUrl;

use Illuminate\Support\Arr as IlluminateArr;

trait Binding
{
    /**
     * @param  array<string, string>  $array
     * @param  array{path: ?bool, query: ?bool, fragment: ?bool}  $options
     */
    public function binding(
        array $binding,
        array $options = []
    ): static {
        $options = $options + [
            'path' => true,
            'query' => true,
            'fragment' => true,
        ];

        //

        $components = $this->getComponent();

        if ($options['path'] ?? true) {
            $components['path'] = $this->bindingPath($binding);
        }

        if ($options['query'] ?? true) {
            $components['query'] = $this->bindingQuery($binding);
        }

        if ($options['fragment'] ?? true) {
            $components['fragment'] = $this->bindingFragment($binding);
        }

        //

        $self = $this->self();
        $self->setComponent($components);

        return $self;
    }

    /**
     * @param  array<string, string>  $binding
     */
    protected function bindingPath(array $binding): string
    {
        $path = $this->getComponent('path');

        $path = str_replace(
            array_keys($binding),
            array_values($binding),
            (string)$path
        );

        return $path;
    }

    /**
     * @param  array<string, string>  $binding
     */
    protected function bindingQuery(array $binding): array
    {
        $query = $this->getComponent('query');

        $res = [];
        foreach ($query as $key => $val) {
            if (! is_string($val)) {
                $res[$key] = $val;

                continue;
            }

            $res[$key] = str_replace(
                array_keys($binding),
                array_values($binding),
                $val
            );
        }

        return $res;
    }

    /**
     * @param  array<string, string>  $binding
     */
    protected function bindingFragment(array $binding): string|array|null
    {
        $fragment = $this->getComponent('fragment');
        if (empty($fragment)) {
            return null;
        }

        if (is_string($fragment)) {
            return str_replace(
                array_keys($binding),
                array_values($binding),
                $fragment
            );
        } elseif (is_array($fragment)) {
            $res = IlluminateArr::dot($fragment);

            foreach ($res as $key => $val) {
                $res[$key] = str_replace(
                    array_keys($binding),
                    array_values($binding),
                    $val
                );
            }

            return IlluminateArr::undot($res);
        }

        return null;
    }
}
