<?php

namespace One23\Helpers;

use Carbon\CarbonInterface;

abstract class AbstractDatetime
{
    abstract public static function parse(mixed $date, ?string $format = null): ?CarbonInterface;

    public static function toDateString(mixed $value, bool $defaultNow = false): ?string
    {
        $res = static::parse($value);
        if (! $res) {
            if (! $defaultNow) {
                return null;
            }

            $res = static::parse('now');
        }

        return $res->toDateString();
    }

    public static function toDateTimeString(mixed $value, bool $defaultNow = false): ?string
    {
        $res = static::parse($value);
        if (! $res) {
            if (! $defaultNow) {
                return null;
            }

            $res = static::parse('now');
        }

        return $res->toDateTimeString();
    }

    protected static function foreachValues(
        \Closure $closure,
        mixed ...$values
    ): void {
        foreach ($values as $value) {
            $closure(static::parse($value));
        }
    }

    public static function where(string $rule, ?string $format, mixed $first, mixed ...$values): bool
    {
        $values = static::map($first, ...$values);
        /** @var CarbonInterface $first */
        $first = array_shift($values);
        if (! $first) {
            return false;
        }

        $date = $format ? $first->format($format) : null;
        foreach ($values as $value) {
            /** @var CarbonInterface|null $value */
            if (! $value) {
                continue;
            }

            if (is_null($format)) {
                if (! $first->{$rule}($value)) {
                    return false;
                }

                continue;
            }

            $val = $value->format($format);
            switch ($rule) {
                case 'eq':
                    if ($val !== $date) {
                        return false;
                    }
                    break;

                case 'lt':
                    if ($date >= $val) {
                        return false;
                    }
                    break;

                case 'lte':
                    if ($date > $val) {
                        return false;
                    }
                    break;

                case 'gt':
                    if ($date <= $val) {
                        return false;
                    }
                    break;

                case 'gte':
                    if ($date < $val) {
                        return false;
                    }
                    break;
            }
        }

        return true;
    }

    public static function eq(mixed $first, mixed ...$values): bool
    {
        return static::where('eq', null, $first, ...$values);
    }

    public static function lt(mixed $first, mixed ...$values): bool
    {
        return static::where('lt', null, $first, ...$values);
    }

    public static function lte(mixed $first, mixed ...$values): bool
    {
        return static::where('lte', null, $first, ...$values);
    }

    public static function gt(mixed $first, mixed ...$values): bool
    {
        return static::where('gt', null, $first, ...$values);
    }

    public static function gte(mixed $first, mixed ...$values): bool
    {
        return static::where('gte', null, $first, ...$values);
    }

    public static function toImmutable(mixed ...$values): array
    {
        $res = [];
        static::foreachValues(
            function(?CarbonInterface $date) use (&$res) {
                $res[] = $date?->toImmutable();
            },
            ...$values
        );

        return $res;
    }

    /**
     * @return array<CarbonInterface|null>
     */
    public static function map(mixed ...$values): array
    {
        $res = [];
        static::foreachValues(
            function(?CarbonInterface $date) use (&$res) {
                $res[] = $date;
            },
            ...$values
        );

        return $res;
    }

    public static function min(mixed ...$values): ?CarbonInterface
    {
        $res = null;
        static::foreachValues(
            function(?CarbonInterface $date) use (&$res) {
                if (! $date) {
                    return;
                }

                if (is_null($res)) {
                    $res = $date;
                } elseif ($date->lt($res)) {
                    $res = $date;
                }
            },
            ...$values
        );

        return $res;
    }

    public static function max(mixed ...$values): ?CarbonInterface
    {
        $res = null;
        static::foreachValues(
            function(?CarbonInterface $date) use (&$res) {
                if (! $date) {
                    return;
                }

                if (is_null($res)) {
                    $res = $date;
                } elseif ($date->gt($res)) {
                    $res = $date;
                }
            },
            ...$values
        );

        return $res;
    }

    /**
     * @return array<CarbonInterface|null>
     */
    public static function minMax(mixed ...$values): array
    {
        $min = static::min(...$values);
        $max = static::max(...$values);

        return [$min, $max];
    }
}
