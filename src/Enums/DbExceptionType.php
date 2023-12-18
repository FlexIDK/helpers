<?php

namespace One23\Helpers\Enums;

enum DbExceptionType: int
{
    case Select = 0;
    case Insert = 1;
    case Update = 2;
    case Delete = 3;
}
