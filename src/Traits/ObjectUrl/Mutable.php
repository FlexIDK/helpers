<?php

namespace One23\Helpers\Traits\ObjectUrl;

trait Mutable
{
    protected bool $mutable = true;

    protected function self(): static
    {
        return $this->getMutable() ? $this : $this->clone();
    }

    public function setMutable(bool $mutable = true): static
    {
        $this->mutable = $mutable;

        return $this;
    }

    public function getMutable(): bool
    {
        return $this->mutable;
    }

    public function clone(): static
    {
        return new static(
            $this,
            $this->options
        );
    }
}
