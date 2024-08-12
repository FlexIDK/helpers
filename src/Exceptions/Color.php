<?php

namespace One23\Helpers\Exceptions;

use One23\Helpers\Exception as BaseException;

class Color extends BaseException
{
    const INVALID_RGB_FORMAT = 1;

    const INVALID_RGB_FORMAT_COUNT = 2;

    const INVALID_COLOR_VALUE = 3;

    const INVALID_HEX_FORMAT = 4;

    const ERROR_HSL_CONVERT = 5;

    const INVALID_CMYK_FORMAT_COUNT = 6;

    const INVALID_CMYK_FORMAT = 7;
}
