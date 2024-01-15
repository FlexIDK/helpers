<?php

namespace One23\Helpers\Traits\ObjectUrl;

/**
 * @method getOptions
 *
 * @property array $components
 */
trait Fragment
{
    protected function value2fragment(
        ?string $val = null,
        array $options = [],
    ) {
        if (! $val) {
            return null;
        }

        return rawurldecode($val);
    }

    protected function fragment2build(
        array $components
    ): string {
        if (! ($components['fragment'] ?? null)) {
            return '';
        }

        return '#' . rawurlencode($components['fragment']);
    }

    public function setFragment(
        ?string $val = null
    ): static {
        $this->components['fragment'] = $this->value2fragment($val);

        return $this;
    }

    public function getFragment(): ?string
    {
        return $this->components['fragment'] ?? null;
    }
}
