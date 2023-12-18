<?php

namespace One23\Helpers;

use Illuminate\Database\Eloquent\Model;
use One23\Helpers\Exceptions\Db as Exception;

class Db
{
    protected static function modelException(
        Model $model,
        Enums\DbExceptionType $dbExceptionType,
    ) {
        $table = $model->getTable();

        switch ($dbExceptionType) {
            case Enums\DbExceptionType::Delete:
                throw new Exception(
                    "fail delete from '{$table}'",
                    Exception::CODE_DELETE
                );
                break;

            case Enums\DbExceptionType::Insert:
                throw new Exception(
                    "fail insert into '{$table}'",
                    Exception::CODE_INSERT
                );
                break;

            case Enums\DbExceptionType::Update:
                throw new Exception(
                    "fail update in '{$table}'",
                    Exception::CODE_UPDATE
                );
                break;

            default:
                throw new Exception(
                    "fail select from '{$table}'",
                    Exception::CODE_SELECT
                );
        }
    }

    public static function update(
        Model $model,
        bool $throw_exception = true,
    ): bool {
        if (! $model->update()) {
            if ($throw_exception) {
                static::modelException($model, Enums\DbExceptionType::Update);
            }

            return false;
        }

        return true;
    }

    public static function insert(
        Model $model,
        bool $throw_exception = true,
    ): bool {
        if (! $model->save()) {
            if ($throw_exception) {
                static::modelException($model, Enums\DbExceptionType::Insert);
            }

            return false;
        }

        return true;
    }

    public static function delete(
        Model $model,
        bool $throw_exception = true,
    ): bool {
        if (! $model->delete()) {
            if ($throw_exception) {
                static::modelException($model, Enums\DbExceptionType::Delete);
            }

            return false;
        }

        return true;
    }

    public static function transaction(
        \Closure $func,
        mixed $if = null,
        bool $foreignKeyChecks = false,
        bool $forceRollback = false,
        ?string $connectionName = null,
    ) {
        $if = Value::val($if);
        if ($if === false) {
            return null;
        }

        /** @phpstan-ignore-next-line */
        $db = \DB::connection($connectionName);
        if ($db->transactionLevel()) {
            return $func();
        } else {
            $db->beginTransaction();
            if ($foreignKeyChecks) {
                $db->query('SET foreign_key_checks = 0');
            }

            try {
                $return = $func();

                if ($foreignKeyChecks) {
                    $db->query('SET foreign_key_checks = 1');
                }

                if (! $forceRollback) {
                    $db->commit();
                } else {
                    $db->rollBack();
                }
            } catch (\Exception $exception) {
                if ($foreignKeyChecks) {
                    $db->query('SET foreign_key_checks = 1');
                }

                $db->rollBack();

                throw new Exception(
                    'fail transaction: ' . $exception->getMessage(),
                    Exception::CODE_TRANSACTION,
                    $exception
                );
            }

            return $return ?? null;
        }
    }

    public static function arr2json(
        array $data,
        string $jsonField = 'data'
    ): array {
        $fields = [];

        foreach ($data as $key => $value) {
            $fields["{$jsonField}->{$key}"] = $value;
        }

        return $fields;
    }
}
