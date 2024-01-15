<?php

namespace One23\Helpers\Traits\ObjectUrl;

use One23\Helpers\Exceptions\Url as Exception;
use One23\Helpers\Number;

/**
 * @method getOptions
 *
 * @property array $components
 */
trait Port
{
    /**
     * @param array{
     *     acceptPort: ?bool,
     * } $options
     */
    protected function value2port(
        ?int $val = null,
        array $options = [],
    ): ?int {
        $options = $this->getOptions($options);

        if (is_bool($options['acceptPort'] ?? null)) {
            if (
                $options['acceptPort']
                && ! isset($val)
            ) {
                throw new Exception('Undefined port', Exception::DENY_WITHOUT_PORT);
            }

            if (
                ! $options['acceptPort']
                && isset($val)
            ) {
                throw new Exception('Port is not allowed', Exception::DENY_WITH_PORT);
            }
        }

        if (
            $val &&
            is_null(
                Number::int($val, null, 1, 65535)
            )
        ) {
            throw new Exception('Invalid port', Exception::INVALID_URL_PORT);
        }

        return $val;
    }

    public function getPort(): ?int
    {
        return $this->components['port'] ?? null;
    }

    public function hasPort(): bool
    {
        return (bool)($this->getPort());
    }

    public function setPort(
        ?int $val = null,
        array $options = [],
    ): static {
        $this->components['port'] = $this->value2port($val, $options);

        return $this;
    }

    protected function port2build(array $components): string
    {
        $port = $components['port'] ?? null;

        if (! $port) {
            return '';
        }

        return ':' . $port;
    }
}
