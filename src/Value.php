<?php

namespace One23\Helpers;

use One23\Helpers\Exceptions\Value as Exception;

class Value
{
    public static function val($value, ...$args)
    {
        if (is_resource($value)) {
            throw new Exception('Resource is not supported');
        }

        if (
            is_string($value)
            || is_numeric($value)
            || is_bool($value)
            || is_array($value)
            || is_null($value)
        ) {
            return $value;
        }

        if ($value instanceof \Closure) {
            return static::val(
                $value(...$args)
            );
        }

        if ($value instanceof \Stringable) {
            return (string)$value;
        }

        if (is_object($value) && method_exists($value, 'toString')) {
            return $value->toString();
        }

        if (is_object($value) && method_exists($value, 'toArray')) {
            return (array)$value->toArray();
        }

        if ($value instanceof \UnitEnum) {
            return $value instanceof \BackedEnum
                ? $value->value
                : $value->name;
        }

        return $value;
    }

    public static function bool($val, bool $hasNull = false): ?bool
    {
        $val = static::val($val);
        if ($hasNull && is_null($val)) {
            return null;
        }

        //

        $acceptable = [true, 1, '1'];
        if (in_array($val, $acceptable, true)) {
            return true;
        }

        $acceptable = [false, 0, '0'];
        if (in_array($val, $acceptable, true)) {
            return false;
        }

        //

        return (bool)$val;
    }
}
