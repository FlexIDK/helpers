<?php

namespace One23\Helpers\Traits\ObjectUrl;

trait Fragment
{
    protected function value2fragment(
        string|array|null $val = null,
        array $options = [],
    ) {
        if (empty($val)) {
            return null;
        }

        if (is_array($val)) {
            return $val;
        }

        // detect encode && decode
        if (preg_match('/%[0-9a-f]{2}/', $val)) {
            $val = rawurldecode($val);
        }

        return $val;
    }

    protected function fragment2build(
        array $components
    ): string {
        if (empty($components['fragment'] ?? null)) {
            return '';
        }

        if (is_array($components['fragment'])) {
            return $this->query2string($components['fragment']);
        } elseif (is_string($components['fragment'])) {
            return rawurlencode($components['fragment']);
        }

        return '';
    }

    public function setFragment(
        string|array|null $val = null
    ): static {
        $self = $this->self();
        $self->setComponent(
            'fragment',
            $this->value2fragment($val)
        );

        return $self;
    }

    public function getFragment(): ?string
    {
        $fragment = $this->getComponent('fragment');
        if (empty($fragment)) {
            return null;
        }

        $res = $this->fragment2build(
            $this->getComponent()
        );

        return ! empty($res)
            ? $res
            : null;
    }
}
