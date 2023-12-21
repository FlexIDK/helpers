<?php

namespace One23\Helpers;

use Carbon\CarbonInterface;

class ObjectDate
{
    protected ?int $maxDays = null;

    protected ?CarbonInterface $min = null;

    protected ?CarbonInterface $max = null;

    protected ?bool $sliceBegin = null;

    protected ?CarbonInterface $defaultFrom = null;

    protected ?CarbonInterface $defaultTo = null;

    protected ?CarbonInterface $defaultDate = null;

    /**
     * @param  array{maxDays: ?int, min: ?mixed, max: ?mixed, sliceBegin: bool, defaultFrom: ?mixed, defaultTo: ?mixed, defaultDate: ?mixed}|null  $options
     */
    public function __construct(array $options = [])
    {
        if (
            ($options['defaultFrom'] ?? null) ||
            ($options['defaultTo'] ?? null)
        ) {
            $this->setDefaultFromTo(
                $options['defaultFrom'] ?? null,
                $options['defaultTo'] ?? null,
            );
        }

        if ($options['defaultDate'] ?? null) {
            $this->setDefaultDate($options['defaultDate']);
        }

        if (
            ($options['min'] ?? null) ||
            ($options['max'] ?? null)
        ) {
            $this->setMinMax(
                $options['min'] ?? null,
                $options['max'] ?? null,
            );
        }

        if ($options['maxDays'] ?? null) {
            $this->setMaxDays($options['maxDays']);
        }

        if (is_bool($options['sliceBegin'] ?? null)) {
            $this->setSliceBegin($options['sliceBegin']);
        }
    }

    public function setDefaultDate(mixed $date = null): static
    {
        $this->defaultDate = Date::parse($date);

        return $this;
    }

    public function setDefaultFromTo(mixed $from = null, mixed $to = null): static
    {
        if ($from && $to) {
            [$this->defaultFrom, $this->defaultTo] = Date::minMax($from, $to);
        } else {
            $this->defaultFrom = Date::parse($from);
            $this->defaultTo = Date::parse($to);
        }

        return $this;
    }

    public function setMinMax(mixed $min = null, mixed $max = null): static
    {
        if ($min && $max) {
            [$this->min, $this->max] = Date::minMax($min, $max);
        } else {
            $this->min = Date::parse($min);
            $this->max = Date::parse($max);
        }

        return $this;
    }

    public function getMinMax(): array
    {
        if ($this->min && $this->max) {
            [$min, $max] = Date::minMax($this->min, $this->max);

            return [$min, $max];
        }

        return [$this->min, $this->max];
    }

    public function setMaxDays(?int $days = null): static
    {
        $days = abs($days);
        $this->maxDays = $days > 0
            ? $days
            : null;

        return $this;
    }

    public function setSliceBegin(?bool $sliceBegin = null): static
    {
        $this->sliceBegin = $sliceBegin;

        return $this;
    }

    public function limitMinMax(
        mixed $date,
        ?bool $isMax = null
    ): CarbonInterface {
        $date = Date::parse($date);
        [$min, $max] = $this->getMinMax();

        if (! $date && is_bool($isMax)) {
            if ($isMax) {
                $date = $max;
            } else {
                $date = $min;
            }
        }

        if ($min) {
            $date = Date::max($date, $this->min);
        }

        if ($max) {
            $date = Date::min($date, $this->max);
        }

        return $date;
    }

    /**
     * @return array<?CarbonInterface, ?CarbonInterface, ?CarbonInterface>
     */
    public function date(mixed $date = null): array
    {
        $date = Date::parse($date);

        if (! $date) {
            if ($this->defaultDate) {
                $date = $this->defaultDate;
            }
        }

        [$min, $max] = $this->getMinMax();
        $date = $date ? $this->limitMinMax($date) : null;

        return Date::map(
            null,
            $date,
            $min,
            $max,
        );
    }

    /**
     * @return array<?CarbonInterface, ?CarbonInterface, ?CarbonInterface, ?CarbonInterface>
     */
    public function fromTo(mixed $from = null, mixed $to = null): array
    {
        [$from, $to] = Date::map(
            null,
            $from, $to
        );

        if (! $from && ! $to) {
            if ($this->defaultTo) {
                $to = $this->defaultTo;
            } else {
                $from = $this->defaultFrom;
            }
        }

        //

        $sliceBegin = $this->sliceBegin;
        if (is_null($sliceBegin)) {
            if ($to && ! $from) {
                $sliceBegin = false;
            } else {
                $sliceBegin = true;
            }
        }

        //

        [$min, $max] = $this->getMinMax();

        $from = $from ? $this->limitMinMax($from, false) : null;
        $to = $to ? $this->limitMinMax($to, true) : null;

        //

        if ($from && $to) {
            [$from, $to] = Date::minMax($from, $to);
        }

        if (
            ! $this->maxDays ||
            (! $from && ! $to)
        ) {
            return [
                $from,
                $to,
                $min,
                $max,
            ];
        }

        //

        [$from, $to] = Date::toImmutable($from, $to);

        if ($to && ! $from) {
            $from = $to->subDays($this->maxDays - 1);

            if ($min) {
                $from = Date::max($from, $min);
            }
        }

        if ($from && ! $to) {
            $to = $from->addDays($this->maxDays - 1);

            if ($max) {
                $to = Date::min($to, $max);
            }
        }

        $days = $to->diffInDays($from);
        if ($days > $this->maxDays) {
            if ($sliceBegin) {
                $to = $from->addDays($this->maxDays - 1);
            } else {
                $from = $to->subDays($this->maxDays - 1);
            }
        }

        return [
            $from,
            $to,
            $min,
            $max,
        ];
    }
}
