<?php

namespace One23\Helpers;

use One23\Helpers\Exceptions\Options as Exception;

/**
 * @todo requried test
 */
class Options
{
    public static function default(
        array $config
    ): array {
        $res = [];

        foreach ($config as $key => $value) {
            if (array_key_exists('default', $value)) {
                $res[$key] = $value['default'];
            }
        }

        return $res;
    }

    /**
     * @param array<int, array{
     *     nullable: ?bool,
     *     type: string,
     *     default: ?mixed,
     *     min: ?int,
     *     max: ?int,
     * }> $config
     */
    public static function all(
        array $data,
        array $config,
        bool $defaults = false,
    ): array {
        $res = [];

        foreach ($data as $key => $value) {
            if (! array_key_exists($key, $config)) {
                continue;
            }

            $res[$key] = static::one($value, $config[$key]);
        }

        if ($defaults) {
            $res = $res + static::default($config);
        }

        return $res;
    }

    /**
     * @param array{
     *     nullable: ?bool,
     *     type: string,
     *     default: ?mixed,
     *     min: ?int,
     *     max: ?int,
     * } $config
     */
    public static function one(
        mixed $value,
        array $config
    ): mixed {
        if (! array_key_exists('type', $config)) {
            throw new Exception('Missing `type`', Exception::UNDEFINED_TYPE);
        }

        if (
            array_key_exists('nullable', $config) &&
            $config['nullable'] &&
            $value === null
        ) {
            return null;
        }

        if ($value === null) {
            throw new Exception('Value is `null`', Exception::INVALID_VALUE_IS_NULL);
        }

        switch ($config['type']) {
            case 'str':
            case 'string':
                if (! is_string($value)) {
                    throw new Exception('Value is not string', Exception::INVALID_VALUE_IS_NOT_STR);
                }

                return Str::val(
                    $value,
                    $config['default'] ?? null,
                );

            case 'int':
            case 'integer':
                if (! is_int($value)) {
                    throw new Exception('Value is not integer', Exception::INVALID_VALUE_IS_NOT_INT);
                }

                return Integer::get(
                    $value,
                    $config['default'] ?? null,
                    $config['min'] ?? null,
                    $config['max'] ?? null
                );

            case 'number':
            case 'float':
                if (
                    ! is_float($value) &&
                    ! is_int($value)
                ) {
                    throw new Exception('Value is not float/integer', Exception::INVALID_VALUE_IS_NOT_NUMERIC);
                }

                return Number::get(
                    $value,
                    $config['default'] ?? null,
                    $config['min'] ?? null,
                    $config['max'] ?? null
                );

            case 'bool':
                if (! is_bool($value)) {
                    throw new Exception('Value is not boolean', Exception::INVALID_VALUE_IS_NOT_BOOL);
                }

                return Value::bool(
                    $value,
                    (bool)($config['nullable'] ?? false)
                );

            default:
                return $value;
        }
    }
}
