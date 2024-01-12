<?php

namespace One23\Helpers;

use Carbon\Carbon;
use Carbon\CarbonInterface;

class DateBetween
{
    protected CarbonInterface $from;

    protected CarbonInterface $to;

    protected array $cacheAll;

    protected array $cacheData;

    public function __construct(
        protected array $data,
        CarbonInterface $from,
        CarbonInterface $to,
    ) {
        [$from, $to] = Date::minMax(
            $from,
            $to,
        );

        $this->from = $from;
        $this->to = $to;
    }

    public function before(): mixed
    {
        return Value::first(
            $this->dataBefore(),
            $this->dataFirst(),
            $this->dataAfter(),
        );
    }

    public function start(): mixed
    {
        $fk = $this->keyFirst();
        if (
            $fk &&
            $fk === $this->from->toDateString()
        ) {
            return $this->data()[$fk];
        }

        return Value::first(
            $this->dataBefore(),
            $this->dataFirst(),
            $this->dataAfter(),
        );
    }

    public function end(): mixed
    {
        return Value::first(
            $this->dataLast(),
            $this->dataBefore(),
            $this->dataAfter(),
        );
    }

    public function after(): mixed
    {
        return Value::first(
            $this->dataAfter(),
            $this->dataLast(),
            $this->dataFirst(),
            $this->dataBefore(),
        );
    }

    public function all(): array
    {
        if (isset($this->cacheAll)) {
            return $this->cacheAll;
        }

        $res = Date::fillByDays(
            $this->from,
            $this->to,
            null,
        );

        $current = Value::first(
            $this->dataBefore(),
            $this->dataFirst(),
            $this->dataAfter(),
        );

        foreach ($res as $date => $val) {
            if (array_key_exists($date, $this->data())) {
                $current = $this->data()[$date];
            }

            $res[$date] = $current;
        }

        return $this->cacheAll = $res;
    }

    public function get(
        CarbonInterface $date,
    ): mixed {
        $all = $this->all();

        $strDate = $date->toDateString();
        if (array_key_exists($strDate, $all)) {
            return $all[$strDate];
        }

        $strFrom = $this->from->toDateString();
        if ($strDate < $strFrom) {
            return $this->before();
        }

        $strTo = $this->to->toDateString();
        if ($strDate > $strTo) {
            return $this->after();
        }

        return null;
    }

    public function now(): mixed
    {
        return $this->get(Carbon::now());
    }

    //

    protected function data(): array
    {
        if (isset($this->cacheData)) {
            return $this->cacheData;
        }

        $res = Date::fillByDays(
            $this->from,
            $this->to,
            null,
        );

        foreach ($this->data as $date => $val) {
            if (! $val) {
                continue;
            }

            if (! array_key_exists($date, $res)) {
                continue;
            }

            $res[$date] = $val;
        }

        return $this->cacheData = Arr::filterNull($res);
    }

    protected function dataBefore(): mixed
    {
        return $this->data['0000-01-01'] ?? null;
    }

    protected function dataAfter(): mixed
    {
        return $this->data['9999-01-01'] ?? null;
    }

    protected function dataFirst(): mixed
    {
        $key = $this->keyFirst();

        return $key
            ? $this->data()[$key]
            : null;
    }

    protected function dataLast(): mixed
    {
        $key = $this->keyLast();

        return $key
            ? $this->data()[$key]
            : null;
    }

    protected function keyFirst(): ?string
    {
        if (empty($this->data())) {
            return null;
        }

        $minDate = null;
        foreach ($this->data() as $date => $value) {
            if (! $value) {
                continue;
            }

            if (
                str_starts_with($date, '0000-') ||
                str_starts_with($date, '9999-')
            ) {
                continue;
            }

            //

            if (
                $minDate &&
                $minDate < $date
            ) {
                continue;
            }

            $minDate = $date;
        }

        return $minDate;
    }

    protected function keyLast(): ?string
    {
        if (empty($this->data())) {
            return null;
        }

        $maxDate = null;
        foreach ($this->data() as $date => $value) {
            if (! $value) {
                continue;
            }

            if (
                str_starts_with($date, '0000-') ||
                str_starts_with($date, '9999-')
            ) {
                continue;
            }

            //

            if (
                $maxDate &&
                $maxDate > $date
            ) {
                continue;
            }

            $maxDate = $date;
        }

        return $maxDate;
    }
}
