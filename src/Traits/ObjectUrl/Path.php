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

        // remove trailing `/`

        $val = preg_replace(
            '/\/+/', '/',
            $val
        );

        // add first `/`

        if (! str_starts_with($val, '/')) {
            $val = '/' . $val;
        }

        return $val;
    }

    public function setPath(?string $path = null): static
    {
        $options = $this->getOptions();

        //

        $self = $this->self();
        $self->setComponent(
            'path',
            $this->value2path(
                $path,
                $options,
            )
        );

        return $self;
    }

    protected function encodePath(string $path): string
    {
        $pathEncode = (bool)($this->getOptions()['pathEncode'] ?? true);
        if (! $pathEncode) {
            return $path;
        }

        //

        $parts = explode('/', $path);

        //

        (function() use (&$parts) {
            foreach ($parts as &$part) {
                // detect dual encode && decode
                while (preg_match('/%[0-9a-f]{2}/i', $part)) {
                    $part = rawurldecode($part);
                }
            }
        })();

        //

        $chars = $this->pathEncodeSkip();

        //

        $res = [];
        foreach ($parts as $part) {
            $r = rawurlencode($part);
            if (! empty($chars)) {
                $r = str_replace(array_keys($chars), array_values($chars), $r);
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

    /**
     * @deprecated
     *
     * @param  string[]  $chars
     */
    public function setPathCharsDontEncode(?array $chars = null): static
    {
        $self = $this->self();

        $self->setOptions([
            'pathEncodeSkip' => empty($chars)
                ? []
                : $chars,
        ]);

        return $self;
    }

    /**
     * @return string[]
     */
    protected function pathEncodeSkip(): array
    {
        if (($this->getOptions()['pathEncode'] ?? true) === false) {
            return [];
        }

        $chars = $this->getOptions()['pathEncodeSkip'] ?? [];
        if (empty($chars)) {
            return [];
        }

        //

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

        return $res;
    }
}
