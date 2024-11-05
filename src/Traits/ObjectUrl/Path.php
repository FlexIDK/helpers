<?php

namespace One23\Helpers\Traits\ObjectUrl;

trait Path
{
    protected function value2path(
        ?string $val = null,
        array $options = [],
    ): string {
        if (! $val) {
            return '/';
        }

        // detect encode && decode
        if (preg_match('/%[0-9a-f]{2}/', $val)) {
            $val = rawurldecode($val);
        }

        // remover trailing slash
        $path = preg_replace(
            '/\/+/', '/',
            $val
        );

        if (! str_starts_with($path, '/')) {
            $path = '/' . $path;
        }

        return $path;
    }

    public function setPath(?string $path = null): static
    {
        $self = $this->self();
        $self->setComponent(
            'path',
            $this->value2path($path)
        );

        return $self;
    }

    protected function encodePath(string $path): string
    {
        $parts = explode('/', $path);

        $res = [];

        foreach ($parts as $part) {
            $r = rawurlencode($part);
            if (! empty($this->pathCharsDontEncode)) {
                $r = str_replace(array_keys($this->pathCharsDontEncode), array_values($this->pathCharsDontEncode), $r);
            }

            $res[] = $r;
        }

        return implode('/', $res);
    }

    public function getPath(): string
    {
        return $this->path2build($this->getComponent());
    }

    protected function path2build(array $components): string
    {
        $path = $components['path'] ?? null;

        return $this->encodePath(
            $path ?: '/'
        );
    }

    protected array $pathCharsDontEncode = [];

    /**
     * @param  string[]  $chars
     */
    public function setPathCharsDontEncode(array $chars): static
    {
        $res = [];
        foreach ($chars as $char) {
            if (mb_strlen($char) !== 1) {
                continue;
            }

            $enc = rawurlencode($char);
            if ($enc !== $char) {
                $res[$enc] = $char;
            }
        }

        $self = $this->self();
        $self->pathCharsDontEncode = $res;

        return $self;
    }
}
