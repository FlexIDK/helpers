<?php

namespace One23\Helpers;

use Carbon\CarbonInterface;

class Date extends AbstractDatetime
{
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

    public static function parse(mixed $val, ?string $format = null): ?CarbonInterface
    {
        return Datetime::parse($val, $format)?->startOfDay();
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
