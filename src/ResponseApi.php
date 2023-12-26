<?php

namespace One23\Helpers;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Responsable;

class ResponseApi implements \Stringable, Arrayable, Jsonable, Responsable
{
    protected string $resultKey = 'result';

    protected bool $isSuccess = true;

    protected mixed $data = null;

    protected string $errorMessage = '';

    protected int $errorCode = 0;

    /** @var \Closure[] */
    protected static array $globalExtra = [];

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

    public function toArray(): array
    {
        if (
            $this->isSuccess &&
            $this->isRaw
        ) {
            return is_array($this->data)
                ? $this->data
                : [$this->resultKey => $this->data];
        }

        //

        $extra = [];
        foreach (static::$globalExtra as $closure) {
            if (! ($closure instanceof \Closure)) {
                continue;
            }

            $res = $closure();
            if (! is_array($res)) {
                continue;
            }

            $extra = [
                ...$extra,
                ...$res,
            ];
        }

        //

        if ($this->isSuccess) {
            return [
                'success' => $this->isSuccess,
                $this->resultKey => $this->data,
                ...$extra,
            ];
        }

        $res = [
            'success' => $this->isSuccess,
            'error' => [
                'message' => null,
                'code' => null,
            ],
            ...$extra,
        ];

        $res['error'] = [
            ...$res['error'],

            ...(is_array($this->data) ? $this->data : []),

            'message' => $this->errorMessage,
            'code' => $this->errorCode,
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

    protected function isSuccess(bool $val): static
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

    public function getDebug(): bool
    {
        return $this->debug
            ?? (static::$globalDebug ?? false);
    }

    //

    public function pretty(bool $val): static
    {
        $this->pretty = $val;

        return $this;
    }

    public static function setGlobalPretty(bool $val): void
    {
        static::$globalPretty = $val;
    }

    public function getPretty(): bool
    {
        return $this->pretty
            ?? (static::$globalPretty ?? false);
    }

    //

    public static function setGlobalExtra(?\Closure $closure = null): void
    {
        if ($closure === null) {
            static::$globalExtra = [];
        } else {
            static::$globalExtra[] = $closure;
        }
    }

    //

    public function setData(mixed $val): static
    {
        $this->data = $val;

        return $this;
    }

    public function isRaw(bool $val): static
    {
        $this->isRaw = $val;

        return $this;
    }

    public function setError(string $message, ?int $code = null): static
    {
        $this->isSuccess(false);

        $this->errorMessage = $message;
        $this->errorCode = $code;

        return $this;
    }

    protected function exception2array(\Throwable $e): array
    {
        return Arr::filterNull([
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => ($this->getDebug() ? $e->getFile() : null),
            'line' => ($this->getDebug() ? $e->getLine() : null),
            'trace' => ($this->getDebug() ? $e->getTraceAsString() : null),
        ]);
    }

    //

    public static function raw(mixed $data): ResponseApi
    {
        return (new static())
            ->isSuccess(true)
            ->setData($data)
            ->isRaw(true);
    }

    public static function ok(mixed $data): ResponseApi
    {
        return (new static())
            ->isSuccess(true)
            ->setData($data);
    }

    public static function error(string $message, ?int $code = null, array $data = []): ResponseApi
    {
        return (new static())
            ->setError($message, $code)
            ->setData([
                'fields' => [],
                ...$data,
            ]);
    }

    public static function exception(\Throwable $e, ?string $message = null, ?int $code = null): ResponseApi
    {
        $previous = $e->getPrevious();

        $self = (new static())
            ->setError($message ?: $e->getMessage(), $code ?: $e->getCode());

        return $self
            ->setData(Arr::filterNull([
                'exception' => $self->exception2array($e),
                'exception_previous' => ($previous ? $self->exception2array($previous) : null),
            ]));
    }
}
