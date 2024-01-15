<?php

namespace One23\Helpers;

class Random
{
    const ALPHA = 'abcdefghijklmnopqrstuvwxyz';

    const DIGITAL = '0123456789';

    const BASE58 = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';

    public function __construct(protected string $charters) {}

    public function generation(int $length): string
    {
        $cnt = mb_strlen($this->charters);

        $res = '';
        for ($i = 0; $i < $length; $i++) {
            $rnd = mt_rand(0, $cnt - 1);
            $res .= mb_substr($this->charters, $rnd, 1);
        }

        return $res;
    }

    //

    public static function byte(int $length): string
    {
        return random_bytes($length);
    }

    public static function alpha(
        int $length,
        ?bool $upper = false,
    ): string {
        if ($upper === true) {
            $chars = mb_strtoupper(static::ALPHA);
        } elseif ($upper === false) {
            $chars = static::ALPHA;
        } else {
            $chars = static::ALPHA . mb_strtoupper(static::ALPHA);
        }

        $self = new static($chars);

        return $self->generation($length);
    }

    public static function base58(int $length): string
    {
        $self = new static(
            static::BASE58
        );

        return $self->generation($length);
    }

    public static function base64(int $length): string
    {
        $self = new static(
            static::ALPHA .
            mb_strtoupper(static::ALPHA) .
            static::DIGITAL
        );

        return $self->generation($length);
    }

    public static function digital(int $length): string
    {
        $self = new static(
            static::DIGITAL
        );

        return $self->generation($length);
    }

    public static function alphaDigital(
        int $length,
        ?bool $upper = false,
    ): string {
        if ($upper === true) {
            $chars = mb_strtoupper(static::ALPHA);
        } elseif ($upper === false) {
            $chars = static::ALPHA;
        } else {
            $chars = static::ALPHA . mb_strtoupper(static::ALPHA);
        }

        //

        $self = new static(
            $chars .
            static::DIGITAL
        );

        return $self->generation($length);
    }

    public static function digitalAlpha(
        int $length,
        ?bool $upper = false,
    ): string {
        return static::alphaDigital(
            $length,
            $upper
        );
    }

    public static function hex(int $length): string
    {
        $self = new static(
            static::DIGITAL .
            'abcdef'
        );

        return $self->generation($length);
    }
}
