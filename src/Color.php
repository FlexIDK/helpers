<?php

namespace One23\Helpers;

use Illuminate\Contracts\Support\Arrayable;
use One23\Helpers\Exceptions\Color as Exception;

class Color implements \Stringable, Arrayable
{
    protected int $r;

    protected int $g;

    protected int $b;

    protected ?int $alpha = null;

    public function __construct(
        string|array $val
    ) {
        if (is_array($val)) {
            $this->_fromArray($val);
        } elseif (is_string($val)) {
            $this->_fromString($val);
        }
    }

    protected static function _fromColor2Int(string|int $val, $max = 255): int
    {
        $res = null;
        if (
            is_string($val) &&
            preg_match('/^[0-9a-f]{1,2}$/i', $val)
        ) {
            if (strlen($val) === 1) {
                $val .= $val;
            }

            $res = round(hexdec($val) / 255 * $max);
        } elseif (is_int($val)) {
            $res = Number::int($val, null, 0, $max);
        }

        if (is_null($res)) {
            throw new Exception('Invalid color value', Exception::INVALID_COLOR_VALUE);
        }

        return $res;
    }

    protected static function _normalizeInt(int $val, int $min = 0, int $max = 255): float
    {
        return ($val - $min) / ($max - $min);
    }

    protected function _fromString(string $val): void
    {
        if (
            ! preg_match('/^#?([0-9a-f]{3,4})$/i', $val) &&
            ! preg_match('/^#?([0-9a-f]{6})$/i', $val) &&
            ! preg_match('/^#?([0-9a-f]{8})$/i', $val)
        ) {
            throw new Exception('Invalid color format (value)', Exception::INVALID_HEX_FORMAT);
        }

        $val = ltrim($val, '#');
        $len = strlen($val);

        if ($len === 3 || $len === 4) {
            $val = str_split($val, 1);
        }
        if ($len === 6 || $len === 8) {
            $val = str_split($val, 2);
        }

        $this->_fromArray($val);
    }

    protected function _fromArray(array $rgba): void
    {
        $cnt = count($rgba);
        if (
            $cnt < 3 &&
            $cnt > 4
        ) {
            throw new Exception('Invalid color format (attributes count)', Exception::INVALID_RGB_FORMAT_COUNT);
        }

        $r = $g = $b = null;
        foreach ([
            'r' => [0, 'r', 'red'],
            'g' => [1, 'g', 'green'],
            'b' => [2, 'b', 'blue'],
        ] as $var => $keys) {
            foreach ($keys as $key) {
                if (isset($rgba[$key])) {
                    ${$var} = static::_fromColor2Int($rgba[$key]);
                    break;
                }
            }
        }

        $alpha = null;
        $keyA = [3, 'a', 'alpha'];
        foreach ($keyA as $key) {
            if (isset($rgba[$key])) {
                $alpha = static::_fromColor2Int($rgba[$key], 100);
                break;
            }
        }

        if (
            is_null($r) ||
            is_null($g) ||
            is_null($b)
        ) {
            throw new Exception('Invalid color format (attributes value)', Exception::INVALID_RGB_FORMAT);
        }

        $this->r = $r;
        $this->g = $g;
        $this->b = $b;
        $this->alpha = $alpha;
    }

    public function toHex(bool $alpha = false): string
    {
        $res = substr(
            $this->toString(),
            0,
            $alpha ? 9 : 7
        );

        if (
            $alpha &&
            strlen($res) === 7
        ) {
            $res .= 'FF';
        }

        return $res;
    }

    public function toString(): string
    {
        $rgb = sprintf(
            '%02X%02X%02X',
            $this->r, $this->g, $this->b
        );

        if (! is_null($this->alpha)) {
            $rgb .= sprintf('%02X', round($this->alpha / 100 * 255));
        }

        return '#' . $rgb;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @return int[]
     */
    public function toArray(): array
    {
        return Arr::filterNull([
            $this->r,
            $this->g,
            $this->b,
            $this->alpha,
        ]);
    }

    /**
     * @return array{r: int, g: int, b: int}
     */
    public function toRGB(): array
    {
        return array_combine(
            ['r', 'g', 'b'],
            array_slice(
                $this->toArray(),
                0, 3
            )
        );
    }

    /**
     * @return array{r: int, g: int, b: int, a: int}
     */
    public function toRGBA(): array
    {
        $res = $this->toArray();
        if (count($res) === 3) {
            $res[] = 100;
        }

        return array_combine(
            ['r', 'g', 'b', 'a'],
            $res
        );
    }

    public static function fromHex(string $val): static
    {
        return new static($val);
    }

    public static function fromRGB(int|array $rgba, ?int $g = null, ?int $b = null, ?int $alpha = null): static
    {
        if (is_array($rgba)) {
            $val = $rgba;
        } else {
            $val = Arr::filterNull([$rgba, $g, $b]);
            if (count($val) !== 3) {
                throw new Exception('Invalid color format (attributes count)', Exception::INVALID_RGB_FORMAT_COUNT);
            }

            if (! is_null($alpha)) {
                $val[] = $alpha;
            }
        }

        return new static($val);
    }

    /**
     * @return float[]
     */
    public function toHSL(): array
    {
        $r = $this->r / 255;
        $g = $this->g / 255;
        $b = $this->b / 255;

        //

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);

        $d = $max - $min;

        $H = $S = 0;
        $L = ($max + $min) / 2;

        if ($d !== 0) {
            $S = $d / (1 - abs(2 * $L - 1));

            switch ($max) {
                case $r:
                    $H = 60 * fmod((($g - $b) / $d), 6);
                    if ($b > $g) {
                        $H += 360;
                    }
                    break;

                case $g:
                    $H = (($b - $r) / $d + 2) * 60;
                    break;

                case $b:
                    $H = (($r - $g) / $d + 4) * 60;
                    break;
            }
        }

        $res = [
            Number::float(
                round($H, 2),
                min: 0, max: 360
            ),
            Number::float(
                round($S, 2) * 100,
                min: 0, max: 100
            ),
            Number::float(
                round($L, 2) * 100,
                min: 0, max: 100
            ),
        ];

        if (count(Arr::filterNull($res)) !== 3) {
            throw new Exception("Can't convert to HSL", Exception::ERROR_HSL_CONVERT);
        }

        return array_combine(
            ['h', 's', 'l'],
            $res,
        );
    }

    public function toHSLString(): string
    {
        $res = $this->toHSL();

        return "hsl({$res['h']}, {$res['s']}%, {$res['l']}%)";
    }

    public function toRGBString(): string
    {
        $res = $this->toRGBA();

        if ($res['a'] === 100) {
            return "rgb({$res['r']}, {$res['g']}, {$res['b']})";
        }

        return "rgba({$res['r']}, {$res['g']}, {$res['b']}, {$res['a']}%)";
    }

    /**
     * @return float[]
     */
    public function toCMYK(): array
    {
        $c = 255 - $this->r;
        $m = 255 - $this->g;
        $y = 255 - $this->b;
        $black = min($c, $m, $y);

        $c = round(($c - $black) / (255 - $black) * 100);
        $m = round(($m - $black) / (255 - $black) * 100);
        $y = round(($y - $black) / (255 - $black) * 100);
        $k = round($black / 255 * 100);

        return array_combine(
            ['c', 'm', 'y', 'k'],
            [$c, $m, $y, $k]
        );
    }

    public static function fromCMYK(int|array $cmyk, ?int $m = null, ?int $y = null, ?int $k = null): static
    {
        if (is_array($cmyk)) {
            if (count($cmyk) !== 4) {
                throw new Exception('Invalid CMYK format (attributes count)', Exception::INVALID_CMYK_FORMAT_COUNT);
            }

            $c = $m = $y = $k = null;
            foreach ([
                'c' => ['c', 0, 'cyan'],
                'm' => ['m', 1, 'magenta'],
                'y' => ['y', 2, 'yellow'],
                'k' => ['k', 3, 'black'],
            ] as $var => $keys) {
                foreach ($keys as $key) {
                    if (isset($cmyk[$key])) {
                        ${$var} = static::_fromColor2Int($cmyk[$key], 100);
                        break;
                    }
                }
            }
        } else {
            $c = $cmyk;
        }

        if (
            is_null($c) ||
            is_null($m) ||
            is_null($y) ||
            is_null($k)
        ) {
            throw new Exception('Invalid color format (attributes value)', Exception::INVALID_CMYK_FORMAT);
        }

        //

        $c = $c / 100;
        $m = $m / 100;
        $y = $y / 100;
        $k = $k / 100;

        $c = $c * (1 - $k) + $k;
        $m = $m * (1 - $k) + $k;
        $y = $y * (1 - $k) + $k;

        $r = 1 - $c;
        $g = 1 - $m;
        $b = 1 - $y;

        //

        return static::fromRGB(
            round(255 * $r),
            round(255 * $g),
            round(255 * $b),
        );
    }
}
