<?php

namespace One23\Helpers;

use Carbon\Carbon;
use Carbon\CarbonInterface;

class Datetime extends AbstractDatetime
{
    public static function parse(mixed $val, ?string $format = null): ?CarbonInterface
    {
        $res = null;

        if (
            $val instanceof CarbonInterface ||
            $val instanceof \DateTime
        ) {
            $res = Carbon::make($val);
        } elseif (is_numeric($val)) {
            $res = Carbon::createFromTimestamp($val);
        } elseif (is_string($val)) {
            if ($format) {
                $res = Carbon::createFromFormat($format, $val);
            } else {
                try {
                    $res = Carbon::make($val);
                } catch (\Exception $exception) {
                    return null;
                }
            }
        }

        if (! $res) {
            return null;
        }

        return $res->toImmutable();
    }

    public static function fillByHours(
        mixed $from,
        mixed $to,
        mixed $default = null,
    ): array {
        [$from, $to] = static::map(
            function(?CarbonInterface $date) {
                return $date?->startOfHour();
            },
            $from, $to,
        );

        return static::fill(
            $from, $to,
            key: function(CarbonInterface $current) {
                return $current->format('Y-m-d H:i:s');
            },
            default: $default,
            step: function(CarbonInterface $current) {
                return $current->addHour();
            }
        );
    }

    public static function toString(mixed $value, mixed $default = null): ?string
    {
        return static::toDateTimeString($value, $default);
    }

    public static function toDateTimeString(mixed $value, mixed $default = null): ?string
    {
        $res = static::val($value, $default);
        if (is_null($res)) {
            return null;
        }

        return $res->toDateTimeString();
    }
}
