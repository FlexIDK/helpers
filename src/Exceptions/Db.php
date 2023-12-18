<?php

namespace One23\Helpers\Exceptions;

use One23\Helpers\Exception as BaseException;

class Db extends BaseException
{
    const CODE_SELECT = 1;

    const CODE_INSERT = 2;

    const CODE_UPDATE = 3;

    const CODE_DELETE = 4;

    const CODE_TRANSACTION = 5;
}
