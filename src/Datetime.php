<?php

namespace One23\Helpers;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use One23\Helpers\Exceptions\Datetime as Exception;

class Datetime extends AbstractDatetime
{
    public static function parse(mixed $date, ?string $format = null): ?CarbonInterface
    {
        $res = null;

        if (
            $date instanceof CarbonInterface ||
            $date instanceof \DateTime
        ) {
            $res = Carbon::make($date);
        } elseif (is_numeric($date)) {
            $res = Carbon::createFromTimestamp($date);
        } elseif (is_string($date)) {
            if ($format) {
                $res = Carbon::createFromFormat($format, $date);
            } else {
                try {
                    $res = Carbon::make($date);
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
}
