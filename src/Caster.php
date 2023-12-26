<?php

namespace One23\Helpers;

use Illuminate\Support\Arr as IlluminateArr;
use One23\Helpers\Exceptions\Caster as Exception;

class Caster
{
    public static function config(
        array $data,
        array $config,
        bool $onlyConfigKeys = false,
    ): array {
        $res = [];

        foreach ($data as $key => $val) {
            if (
                $onlyConfigKeys &&
                ! array_key_exists($key, $config)
            ) {
                continue;
            }

            $type = null;
            $filters = null;

            if (array_key_exists($key, $config)) {
                if (
                    $config[$key] instanceof Enums\CasterType
                ) {
                    $type = $config[$key];
                } elseif (is_array($config[$key])) {
                    $type = $config[$key]['type'] ?? null;
                    $filters = IlluminateArr::wrap($config[$key]['filter'] ?? []);
                }
            }

            $res[$key] = static::val($val, $type, $filters);
        }

        return $res;
    }

    public static function val(mixed $val, ?Enums\CasterType $type = null, ?array $filters = null)
    {
        $val = Value::val($val);

        if ($type) {
            $val = match (self::normalizeType($type)) {
                Enums\CasterType::Boolean => Value::bool($val, true),
                Enums\CasterType::Str => Str::val($val, null, false),
                Enums\CasterType::Int => Number::int($val),
                Enums\CasterType::Float => Number::float($val),

                Enums\CasterType::Datetime => Datetime::val($val)
                    ?->toDateTimeString(),
                Enums\CasterType::Date => Date::val($val)
                    ?->toDateString(),
                Enums\CasterType::Carbon => Datetime::val($val),

                Enums\CasterType::Arr => is_array($val) ? $val : [],

                Enums\CasterType::ArrOfInt => array_map(function($v): ?int {
                    return Number::int($v);
                }, is_array($val) ? $val : []),
                Enums\CasterType::ArrOfFloat => array_map(function($v): ?float {
                    return Number::float($v);
                }, is_array($val) ? $val : []),
                Enums\CasterType::ArrOfStr => array_map(function($v): ?string {
                    return Str::val($v, null, false);
                }, is_array($val) ? $val : []),
            };
        }

        return self::applyFilters(
            $val,
            $filters
        );
    }

    protected static function applyFilters(mixed $val, ?array $filters = null): mixed
    {
        if (empty($filters)) {
            return $val;
        }

        foreach ($filters as $filter) {
            $val = self::applyFilter($val, $filter);
        }

        return $val;
    }

    protected static function applyFilter(
        mixed $val,
        \Closure|Enums\CasterFilter|null $filter
    ): mixed {
        if (is_null($filter)) {
            return $val;
        }

        if ($filter instanceof \Closure) {
            return $filter($val);
        }

        switch ($filter) {
            case Enums\CasterFilter::ArrUniqueFloat:
                return array_unique(array_map(function($val) {
                    return Number::float($val);
                }, (is_array($val) ? $val : [])), SORT_NUMERIC);

            case Enums\CasterFilter::ArrUniqueInt:
                return array_unique(array_map(function($val) {
                    return Number::int($val);
                }, (is_array($val) ? $val : [])), SORT_NUMERIC);

            case Enums\CasterFilter::ArrUniqueStr:
                return array_unique(array_map(function($val) {
                    return Str::val($val, null, false);
                }, (is_array($val) ? $val : [])), SORT_STRING);

            case Enums\CasterFilter::ArrNotNull:
                return array_filter(
                    (is_array($val) ? $val : []),
                    function($val) {
                        return $val !== null;
                    }
                );

            case Enums\CasterFilter::ArrValues:
                return array_values(
                    (is_array($val) ? $val : [])
                );

            case Enums\CasterFilter::Gt0:
                return static::apply2value($val, function($val) {
                    return is_numeric($val) && $val > 0
                        ? $val
                        : null;
                });

            case Enums\CasterFilter::Gte0:
                return static::apply2value($val, function($val) {
                    return is_numeric($val) && $val >= 0
                        ? $val
                        : null;
                });

            case Enums\CasterFilter::Trim:
                return static::apply2value($val, function($val) {
                    return Str::val($val);
                });

            case Enums\CasterFilter::Lower:
                return static::apply2value($val, function($val) {
                    return is_string($val) ? mb_strtolower($val) : null;
                });

            case Enums\CasterFilter::Upper:
                return static::apply2value($val, function($val) {
                    return is_string($val) ? mb_strtoupper($val) : null;
                });

            default:
                throw new Exception("Unsupported filter `{$filter->name}`");
        }
    }

    protected static function apply2value(mixed $val, \Closure $closure): mixed
    {
        if (is_array($val)) {
            return array_map(function($val) use ($closure) {
                return $closure($val);
            }, $val);
        } else {
            return $closure($val);
        }
    }

    protected static function normalizeType(string|Enums\CasterType $type): Enums\CasterType
    {
        if ($type instanceof Enums\CasterType) {
            return $type;
        }

        return match ($type) {
            'b', 'bool', 'boolean' => Enums\CasterType::Boolean,
            's', 'str', 'string' => Enums\CasterType::Str,
            'i', 'int', 'integer' => Enums\CasterType::Int,
            'f', 'float', 'double' => Enums\CasterType::Float,
            'a', 'arr', 'array' => Enums\CasterType::Arr,

            'ai', 'arrOfInt', 'arrayOfInt' => Enums\CasterType::ArrOfInt,
            'af', 'arrOfFloat', 'arrayOfFloat' => Enums\CasterType::ArrOfFloat,
            'as', 'arrOfStr', 'arrayOfStr' => Enums\CasterType::ArrOfStr,

            default => throw new Exception("Unsupported type `{$type}`"),
        };
    }
}
