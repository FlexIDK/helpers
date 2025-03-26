<?php

namespace One23\Helpers;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Responsable;

class ResponseApi implements \Stringable, Arrayable, Jsonable, Responsable
{
    protected ?string $resultKey = null;

    protected static string $globalResultKey = 'result';

    protected bool $isSuccess = true;

    protected mixed $data = null;

    protected string $errorMessage = '';

    protected int $errorCode = 0;

    /** @var \Closure[] */
    protected static array $extra = [];

    protected bool $pretty;

    protected static bool $globalPretty;

    protected bool $isRaw = false;

    protected bool $debug;

    protected static bool $globalDebug;

    protected function __construct() {}

    public function __toString()
    {
        return $this->toJson();
    }

    public function toJson($options = 0)
    {
        return json_encode(
            $this->isRaw ? $this->data : $this->toArray(),
            $options ?: ($this->getPretty() ? (JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : 0)
        );
    }

    protected function extra(): array
    {
        $ignoreKeys = [
            'success',
            'error',
            $this->getDebug() ? 'is_debug' : null,
            $this->getResultKey(),
        ];

        $res = [];
        foreach (static::$extra as $closure) {
            if (! ($closure instanceof \Closure)) {
                continue;
            }

            $r = $closure();
            if (! is_array($r)) {
                continue;
            }

            array_walk($r, function($v, $k) use (
                &$res,
                $ignoreKeys,
            ) {
                if (in_array($k, $ignoreKeys, true)) {
                    return;
                }

                $res[$k] = $v;
            });
        }

        return $res;
    }

    public function toArray(): array
    {
        if (
            $this->isSuccess &&
            $this->isRaw
        ) {
            return is_array($this->data)
                ? $this->data
                : [$this->getResultKey() => $this->data];
        }

        $isDebug = $this->getDebug();
        if ($this->isSuccess) {
            return [
                'success' => $this->isSuccess,
                $this->getResultKey() => $this->data,
                ...($isDebug ? ['is_debug' => $this->getDebug()] : []),
                ...$this->extra(),
            ];
        }

        $res = [
            'success' => $this->isSuccess,
            ...($isDebug ? ['is_debug' => $this->getDebug()] : []),
        ];

        $res['error'] = [
            ...[
                'message' => null,
                'code' => null,
            ],

            ...(is_array($this->data) ? $this->data : []),

            'message' => $this->errorMessage,
            'code' => $this->errorCode,
        ];

        // error fields fix types

        if (array_key_exists('fields', $res['error'])) {
            $fields = $res['error']['fields'] ?? [];
            $fields = is_array($fields) ? $fields : [];

            $res['error']['fields'] = $fields;
            $res['error']['fields_keys'] = array_keys($fields);
        }

        $res = [
            ...$res,
            ...$this->extra(),
        ];

        return $res;
    }

    public function toResponse($request): \Illuminate\Http\JsonResponse
    {
        return \Illuminate\Support\Facades\Response::json(
            $this->toArray(),
            200,
            [],
            $this->getPretty() ? (JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : 0
        );
    }

    protected function setIsSuccess(bool $val): static
    {
        $this->isSuccess = $val;

        return $this;
    }

    //

    public function setDebug(bool $val): static
    {
        $this->debug = $val;

        return $this;
    }

    public static function setGlobalDebug(bool $val): void
    {
        static::$globalDebug = $val;
    }

    protected function getDebug(): bool
    {
        return $this->debug
            ?? (static::$globalDebug ?? false);
    }

    //

    public function setResultKey(?string $val = null): static
    {
        $this->resultKey = $val ?: null;

        return $this;
    }

    public static function setGlobalResultKey(string $val): void
    {
        if (! $val) {
            throw new \InvalidArgumentException('Global result key cannot be empty');
        }

        static::$globalResultKey = $val;
    }

    protected function getResultKey(): string
    {
        return $this->resultKey
            ?: static::$globalResultKey;
    }

    //

    public function setPretty(bool $val): static
    {
        $this->pretty = $val;

        return $this;
    }

    public static function setGlobalPretty(bool $val): void
    {
        static::$globalPretty = $val;
    }

    protected function getPretty(): bool
    {
        return $this->pretty
            ?? (static::$globalPretty ?? false);
    }

    //

    public static function setGlobalExtra(\Closure $closure): int
    {
        static::$extra[] = $closure;

        return array_key_last(static::$extra);
    }

    public static function removeGlobalExtra(?int $key = null): void
    {
        if ($key === null) {
            static::$extra = [];
        } else {
            unset(static::$extra[$key]);
        }
    }

    public static function resetGlobalExtra(): void
    {
        static::removeGlobalExtra(null);
    }

    //

    /**
     * @param  string|null  $merge  append|prepend|replace
     */
    public function setData(mixed $val, ?string $merge = null): static
    {
        if (
            $merge &&
            is_array($val) &&
            in_array($merge, ['append', 'prepend', 'replace']) !== false
        ) {
            $before = $this->data ?? [];

            switch ($merge) {
                case 'append':
                    $this->data = array_merge(
                        (is_array($before) ? $before : []),
                        $val
                    );
                    break;

                case 'prepend':
                    $before = $this->data ?? [];
                    $this->data = array_merge(
                        $val,
                        (is_array($before) ? $before : []),
                    );
                    break;

                case 'replace':
                    $this->data = $val;
                    break;
            }
        } else {
            $this->data = $val;
        }

        return $this;
    }

    public function getData(): mixed
    {
        return $this->data ?? null;
    }

    public function isRaw(bool $val): static
    {
        $this->isRaw = $val;

        return $this;
    }

    public function setError(string $message, ?int $code = null): static
    {
        $this->setIsSuccess(false);

        $this->errorMessage = $message;
        $this->errorCode = $code;

        return $this;
    }

    protected function exception2array(\Throwable $e): array
    {
        $trace = null;
        if ($this->getDebug()) {
            $trace = Arr::preview(
                $e->getTrace(),
                3,
                1,
                '...'
            );
        }

        return Arr::filterNull([
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => ($this->getDebug() ? $e->getFile() : null),
            'line' => ($this->getDebug() ? $e->getLine() : null),
            'trace' => $trace,
        ]);
    }

    //

    public static function raw(mixed $data): ResponseApi
    {
        return (new static)
            ->setIsSuccess(true)
            ->setData($data)
            ->isRaw(true);
    }

    public static function ok(mixed $data): ResponseApi
    {
        return (new static)
            ->setIsSuccess(true)
            ->setData($data);
    }

    public static function error(string $message, ?int $code = null, array $data = []): ResponseApi
    {
        return (new static)
            ->setError($message, $code)
            ->setData([
                'fields' => [],
                ...$data,
            ]);
    }

    public static function exception(\Throwable $e, ?string $message = null, ?int $code = null): ResponseApi
    {
        $previous = $e->getPrevious();

        $code = $code ?: $e->getCode();
        $self = (new static)
            ->setError(
                $message ?: $e->getMessage(),
                (is_int($code) ? $code : 0),
            );

        return $self
            ->setData(Arr::filterNull([
                'exception' => $self->exception2array($e),
                'exception_previous' => ($previous ? $self->exception2array($previous) : null),
            ]));
    }
}
