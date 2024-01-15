<?php

namespace One23\Helpers\Exceptions;

use One23\Helpers\Exception as BaseException;

class Options extends BaseException
{
    const UNDEFINED_TYPE = 1;

    const INVALID_VALUE_IS_NULL = 2;

    const INVALID_VALUE_IS_NOT_STR = 3;

    const INVALID_VALUE_IS_NOT_INT = 4;

    const INVALID_VALUE_IS_NOT_NUMERIC = 5;

    const INVALID_VALUE_IS_NOT_BOOL = 6;
}
