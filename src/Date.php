<?php

namespace One23\Helpers;

use Carbon\CarbonInterface;

class Date extends AbstractDatetime
{
    protected const FILL_LIMIT = 1000;

    /**
     * @param  array{maxDays: ?int, min: ?mixed, max: ?mixed, sliceBegin: bool, defaultFrom: ?mixed, defaultTo: ?mixed}|null  $options
     * @return array{CarbonInterface, CarbonInterface, CarbonInterface, CarbonInterface}
     */
    public static function fromTo(
        mixed $from,
        mixed $to,
        array $options = []
    ): array {
        $obj = new ObjectDate($options);

        return $obj->fromTo($from, $to);
    }

    /**
     * @param  array{min: ?mixed, max: ?mixed, defaultDate: ?mixed}|null  $options
     * @return array{CarbonInterface, CarbonInterface, CarbonInterface}
     */
    public static function date(
        mixed $date,
        array $options = []
    ): array {
        $obj = new ObjectDate($options);

        return $obj->date($date);
    }

    public static function parse(mixed $date, ?string $format = null): ?CarbonInterface
    {
        return Datetime::parse($date, $format)?->startOfDay();
    }

    public static function each(
        mixed $from,
        mixed $to,
        \Closure $callback,
        int $days = 1,
    ): void {
        [$from, $to] = static::minMax($from, $to);

        Datetime::each(
            $from, $to, $callback,
            function(CarbonInterface $date) use ($days): CarbonInterface {
                return $date->addDays($days);
            }
        );
    }

    public static function fill(
        mixed $from,
        mixed $to,
        \Closure $key,
        mixed $default = null,
        int $days = 1,
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
            $days
        );

        return $res;
    }

    public static function fillByDays(
        mixed $from,
        mixed $to,
        mixed $default = null
    ): array {
        return static::fill(
            $from, $to,
            function(CarbonInterface $date) {
                return $date->toDateString();
            },
            $default
        );
    }
}
