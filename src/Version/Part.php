<?php

namespace One23\Helpers\Version;

use One23\Helpers\Exceptions\Version as Exception;
use One23\Helpers\Str;

class Part implements \Stringable
{
    const LEVEL_DEFAULT = 0;

    const LEVEL_ALPHA = 1;

    const LEVEL_BETA = 2;

    const LEVEL_RC = 3;

    const LEVEL_RTM = 4;

    const LEVEL_STABLE = 5;

    const LEVEL_RELEASE = 6;

    protected array $levels = [
        self::LEVEL_ALPHA,
        self::LEVEL_BETA,
        self::LEVEL_RC,
        self::LEVEL_RTM,
        self::LEVEL_STABLE,
        self::LEVEL_RELEASE,
        self::LEVEL_DEFAULT,
    ];

    protected array $level2string = [
        self::LEVEL_ALPHA => 'alpha',
        self::LEVEL_BETA => 'beta',
        self::LEVEL_RC => 'rc',
        self::LEVEL_RTM => 'rtm',
        self::LEVEL_STABLE => 'stable',
        self::LEVEL_RELEASE => 'release',
        self::LEVEL_DEFAULT => '',
    ];

    protected int $level;

    protected int $number;

    protected string $original;

    public function __construct(string $val)
    {
        $val = Str::val($val);
        if (is_null($val)) {
            throw new Exception('Invalid `part` format');
        }

        $this->original = $val;
        $this->parse();
    }

    protected function parse(): void
    {
        $val = $this->original;

        //

        $this->number = 0;
        if (preg_match('/[0-9]+/', $val, $match)) {
            $this->number = (int)$match[0];
        }

        $this->level = static::LEVEL_DEFAULT;
        if (preg_match('/([a-z]+)/i', $val, $match)) {
            switch (strtolower($match[1])) {
                case 'b':
                case 'beta':
                    $this->level = static::LEVEL_BETA;
                    break;

                case 'a':
                case 'alpha':
                    $this->level = static::LEVEL_ALPHA;
                    break;

                case 'rc':
                    $this->level = static::LEVEL_RC;
                    break;

                case 'rtm':
                    $this->level = static::LEVEL_RTM;
                    break;

                case 's':
                case 'stable':
                    $this->level = static::LEVEL_STABLE;
                    break;

                case 'r':
                case 'rel':
                case 'release':
                    $this->level = static::LEVEL_RELEASE;
                    break;
            }
        }
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        $level = $this->level2string[$this->level] ?? '';
        $number = $this->number ?? '0';

        return
            ($number ?: (! $level ? '0' : '')) .
            ($number && $level ? '-' : '') .
            ($level ?: '');
    }

    public function getOriginal(): string
    {
        return $this->original;
    }

    public function getLevel(): int
    {
        return array_search($this->level, $this->levels);
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function compare(
        Part|string $part
    ): int {
        if (! $part instanceof Part) {
            $part = new static($part);
        }

        if ($this->getLevel() > $part->getLevel()) {
            return 1;
        }

        if ($this->getLevel() < $part->getLevel()) {
            return -1;
        }

        if ($this->getNumber() > $part->getNumber()) {
            return 1;
        }

        if ($this->getNumber() < $part->getNumber()) {
            return -1;
        }

        return 0;
    }
}
