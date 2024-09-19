<?php

namespace One23\Helpers\Traits\ObjectUrl;

use Illuminate\Support\Arr as IlluminateArr;
use Illuminate\Support\Str as IlluminateStr;
use One23\Helpers\Exceptions\ObjectUrl as Exception;

trait Scheme
{
    /**
     * deprecated defaultScheme
     *
     * @param  array{
     *  defaultScheme: ?string,
     *  onlyHttp: ?bool
     * } $options
     */
    protected function value2scheme(
        ?string $val = null,
        ?array $options = null,
    ): string {
        $options = $this->getOptions($options);

        //

        // @deprecated
        if (! $val) {
            if (! ($options['defaultScheme'] ?? null)) {
                throw new Exception('Undefined scheme', Exception::UNDEFINED_SCHEME);
            }

            /** @deprecated */
            $val = $options['defaultScheme'];
        }

        $val = IlluminateStr::lower($val);
        if (! is_null($options['onlyHttp'] ?? null)) {
            if (
                $options['onlyHttp']
                && ! in_array($val, ['http', 'https'])
            ) {
                throw new Exception('Only HTTP is allowed', Exception::DENY_SCHEME_NOT_HTTP);
            }

            if (
                ! $options['onlyHttp']
                && in_array($val, ['http', 'https'])
            ) {
                throw new Exception('HTTP is not allowed', Exception::DENY_SCHEME_HTTP);
            }
        }

        if (
            ! preg_match('@^[a-z0-9-+.]+$@i', $val)
        ) {
            throw new Exception('Invalid scheme charters', Exception::INVALID_URL_SCHEME_CHARTERS);
        }

        return $val;
    }

    /**
     * deprecated defaultScheme
     *
     * @param  array{
     *      defaultScheme: ?string,
     *      onlyHttp: ?bool
     * } $options
     */
    public function setScheme(
        string $val,
        array $options = []
    ): static {
        $self = $this->self();
        $self->setComponent(
            'scheme',
            $this->value2scheme(
                $val,
                $options
            )
        );

        return $self;
    }

    public function getScheme(): string
    {
        return $this->getComponent('scheme');
    }

    public function isHttp(): bool
    {
        return $this->isScheme(['http', 'https']);
    }

    public function isScheme(array|string $schemes): bool
    {
        $schemes = IlluminateArr::wrap($schemes);

        return in_array(
            $this->getScheme(),
            array_map(
                fn($scheme) => IlluminateStr::lower($scheme),
                $schemes
            )
        );
    }

    protected function scheme2build(array $components): string
    {
        return $components['scheme'] . '://';
    }
}
