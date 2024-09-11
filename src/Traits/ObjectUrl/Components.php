<?php

namespace One23\Helpers\Traits\ObjectUrl;

use One23\Helpers\Exceptions\ObjectUrl as Exception;

trait Components
{
    protected array $components = [];

    protected array $componentsKeys = [
        'scheme', 'host', 'port', 'user', 'pass', 'path', 'query', 'fragment',
    ];

    protected function validateComponentKey(string $key): void
    {
        if (! in_array($key, $this->componentsKeys)) {
            throw new Exception('Invalid component key: ' . $key, Exception::INVALID_COMPONENT_KEY);
        }
    }

    protected function getComponent(?string $key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->components;
        }

        $this->validateComponentKey($key);

        if ($key === 'query') {
            $res = $this->components[$key] ?? $default;
            if (
                ! is_array($res) ||
                empty($res)
            ) {
                return [];
            }

            return $res;
        }

        return $this->components[$key] ?? $default;
    }

    protected function setComponent(string|array $key, $value = null): static
    {
        if (is_array($key)) {
            $this->components = [];

            foreach ($key as $k => $v) {
                $this->setComponent($k, $v);
            }

            return $this;
        }

        //

        $this->validateComponentKey($key);

        $this->components[$key] = $value;

        return $this;
    }
}
