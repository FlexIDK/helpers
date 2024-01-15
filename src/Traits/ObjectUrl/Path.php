<?php

namespace One23\Helpers\Traits\ObjectUrl;

/**
 * @method getOptions
 *
 * * @property array $components
 */
trait Path
{
    protected function value2path(?string $val = null): string
    {
        // remover trailing slash
        return preg_replace(
            '/\/+/', '/',
            $val ?: '/'
        );
    }

    public function setPath(?string $path = null): static
    {
        $this->components['path'] = $this->value2path($path);

        return $this;
    }

    public function getPath(): string
    {
        return $this->components['path'] ?? '/';
    }

    protected function path2build(array $components): string
    {
        $path = $components['path'] ?? null;

        return $path ?: '/';
    }
}
