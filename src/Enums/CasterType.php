<?php

namespace One23\Helpers\Enums;

enum CasterType
{
    case Boolean;
    case Str;
    case Int;
    case Float;
    case Arr;
    case ArrOfInt;
    case ArrOfFloat;
    case ArrOfStr;
    case Date;
    case Datetime;
    case Carbon;
}
