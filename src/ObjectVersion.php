<?php

namespace One23\Helpers;

use Illuminate\Contracts\Support\Arrayable;
use One23\Helpers\Exceptions\Version as Exception;

class ObjectVersion implements \Stringable, Arrayable
{
    protected string $prefix;

    protected array $parts;

    protected string $original;

    public function __construct(ObjectVersion|string $val)
    {
        if (! $val instanceof ObjectVersion) {
            $this->original = $val;
        } else {
            $this->original = $val->getOriginal();
        }

        $this->parse();
    }

    protected function parse(): void
    {
        $val = $this->original;

        //

        if (! preg_match('/^(v|ver|version)?\.?(([0-9]+)(.*))$/i', $val, $matches)) {
            throw new Exception('Invalid version format');
        }

        $this->prefix = $matches[1] ?? '';
        $this->parts = array_map(function($part) {
            $part = Str::val($part);
            if (is_null($part)) {
                throw new Exception('Invalid version `part` format');
            }

            return new Version\Part($part);
        }, explode('.', $matches[2]));
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        $res = [];

        foreach ($this->toArray() as $part) {
            $res[] = $part->toString();
        }

        return implode('.', $res);
    }

    public function toArray(): array
    {
        return $this->parts;
    }

    public function getOriginal(): string
    {
        return $this->original;
    }
}
