<?php

namespace One23\Helpers;

use Carbon\CarbonInterface;
use One23\Helpers\Exceptions\Datetime as Exception;

abstract class AbstractDatetime
{
    protected const FILL_LIMIT = 1000;

    abstract public static function parse(mixed $val, ?string $format = null): ?CarbonInterface;

    public static function val(mixed $val, mixed $default = null): ?CarbonInterface
    {
        $val = Value::val($val);

        $res = static::parse($val);
        if (is_null($res) && !is_null($default)) {
            $res = static::val($default);
        }

        return $res ?: null;
    }

    public static function orNow(mixed $val): CarbonInterface
    {
        return static::val($val, 'now');
    }

    public static function now(): CarbonInterface
    {
        return static::parse('now');
    }

    public static function isBetween(
        mixed $date,
        mixed $from, mixed $to
    ): bool {
        [$date, $from, $to] = static::map(null, $date, $from, $to);

        if (! $date) {
            return false;
        }

        if ($from && $to) {
            [$from, $to] = static::minMax($from, $to);

            return $date->between($from, $to);
        }

        if ($from) {
            return $date->gte($from);
        }

        if ($to) {
            return $date->lte($to);
        }

        return false;
    }

    public static function fill(
        mixed $from,
        mixed $to,
        \Closure $key,
        mixed $default = null,
        ?\Closure $step = null,
    ): array {
        $res = [];
        $i = 0;

        static::each(
            $from, $to,
            function(CarbonInterface $date) use (&$res, &$i, $key, $default) {
                if ($i >= static::FILL_LIMIT) {
                    return false;
                }

                $k = $key($date);
                if (! is_string($k)) {
                    throw new Exception('key must be string');
                }

                $res[$key($date)] = Value::val($default, $date);

                $i++;
            },
            step: $step
        );

        return $res;
    }

    public static function each(
        mixed $from,
        mixed $to,
        \Closure $callback,
        ?\Closure $step = null,
    ): void {
        [$from, $to] = static::minMax($from, $to);

        if ($step === null) {
            $step = function(CarbonInterface $date): CarbonInterface {
                return $date->addDay();
            };
        }

        /**
         * @var CarbonInterface $from
         * @var CarbonInterface $to
         */
        $current = static::parse($from);

        while (true) {
            if (Datetime::gt($current, $to)) {
                break;
            }

            if ($callback($current) === false) {
                break;
            }

            $current = $step($current);
            if (! $current instanceof CarbonInterface) {
                throw new Exception('callable `step` need `CarbonInterface` return');
            }
        }
    }

    public static function toDateString(mixed $value, mixed $default = null): ?string
    {
        $res = static::val($value, $default);
        if (is_null($res)) {
            return null;
        }

        return $res->toDateString();
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
        $values = static::map(
            null,
            $first,
            ...$values
        );
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
        return static::map(
            function(?CarbonInterface $date) {
                return $date?->toImmutable();
            },
            ...$values
        );
    }

    public static function toMutable(mixed ...$values): array
    {
        return static::map(
            function(?CarbonInterface $date) {
                return $date?->toMutable();
            },
            ...$values
        );
    }

    /**
     * @return array<CarbonInterface|null>
     */
    public static function map(?\Closure $callback = null, mixed ...$values): array
    {
        $res = [];
        static::foreachValues(
            function(?CarbonInterface $date) use (&$res, $callback) {
                $res[] = $callback
                    ? $callback($date)
                    : $date;
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
