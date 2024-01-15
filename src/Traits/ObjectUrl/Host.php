<?php

namespace One23\Helpers\Traits\ObjectUrl;

use Illuminate\Support\Str as IlluminateStr;
use One23\Helpers\Exceptions\Url as Exception;
use One23\Helpers\Str;

/**
 * @method getOptions
 *
 * @property array $components
 */
trait Host
{
    /**
     * @param array{
     *     minHostLevel: int,
     *     acceptIp: ?bool,
     *     allowWildcard: ?bool,
     *     hostHuman: ?bool,
     * } $options
     */
    protected function value2host(
        ?string $val = null,
        array $options = [],
    ): string {
        $options = $this->getOptions($options);

        //

        if (! $val) {
            throw new Exception('Undefined host', Exception::UNDEFINED_HOST);
        }

        $val = IlluminateStr::lower($val);

        $val = idn_to_ascii($val);
        if (! $val) {
            throw new Exception('Invalid IDN host', Exception::INVALID_URL_HOST_IDN);
        }

        // ipv4
        if (preg_match('@^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$@', $val)) {
            if (! Str::isIpV4($val)) {
                throw new Exception('Invalid IPv4 host', Exception::INVALID_URL_HOST_IPV4);
            }

            if (($options['acceptIp'] ?? null) === false) {
                throw new Exception('IP is not allowed', Exception::DENY_IPV4);
            }
        }
        // ipv6
        elseif (preg_match('@^\[([a-f0-9:]+)\]$@i', $val, $match)) {
            if (! Str::isIpV6($match[1])) {
                throw new Exception('Invalid IPv6 host', Exception::INVALID_URL_HOST_IPV6);
            }

            if (($options['acceptIp'] ?? null) === false) {
                throw new Exception('IP is not allowed', Exception::DENY_IPV6);
            }
        }
        // hostname
        else {
            if ($options['hostHuman'] ?? false) {
                try {
                    $val = idn_to_utf8($val, 0, INTL_IDNA_VARIANT_UTS46);
                } catch (\Throwable) {
                }
            }

            $host = preg_replace('/^www\./u', '', $val);
            $hostParts = explode('.', $host);

            if (
                ((int)$options['minHostLevel'] ?? 0) > 0 &&
                count(array_filter($hostParts)) < (int)$options['minHostLevel']
            ) {
                throw new Exception('Host level is too low', Exception::INVALID_URL_HOST_LEVEL);
            }

            array_walk(
                $hostParts,
                function(&$part) use (
                    $options
                ) {
                    $part = idn_to_ascii($part);

                    if (
                        $part === '*' &&
                        $options['allowWildcard'] ?? false
                    ) {
                        return;
                    }

                    if (! preg_match('@^[a-z0-9](([a-z0-9-]+)?[a-z0-9])?$@i', $part)) {
                        throw new Exception('Invalid host charters', Exception::INVALID_URL_HOST_CHARTERS);
                    }
                }
            );
        }

        return $val;
    }

    /**
     * @param  array{
     *     minHostLevel: int,
     *     acceptIp: ?bool,
     *     allowWildcard: ?bool,
     *     hostHuman: ?bool,
     * }  $options
     */
    public function setHost(
        string $host,
        array $options = [],
    ): static {
        $options = $this->getOptions($options);

        $this->components['host'] = $this->value2host(
            $host,
            $options,
        );

        return $this;
    }

    public function getHost(bool $human = false): string
    {
        return $human
            ? $this->getHostHuman()
            : $this->components['host'];
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

    public function getHost1Level(bool $human = false): string
    {
        if ($this->isIp()) {
            return $this->getHost();
        }

        $host = $this->getHost($human);

        return implode(
            '.',
            array_slice(
                explode('.', $host),
                -1,
                1
            )
        );
    }

    public function getHost2level(bool $human = false): string
    {
        if ($this->isIp()) {
            return $this->getHost();
        }

        $host = $this->getHost($human);

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

    protected function host2build(array $components): string
    {
        return $components['host'];
    }
}
