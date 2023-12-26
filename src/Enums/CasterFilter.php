<?php

namespace One23\Helpers\Enums;

enum CasterFilter
{
    case Trim;
    case Lower;
    case Upper;
    case Gt0;
    case Gte0;
    case ArrUniqueInt;
    case ArrUniqueFloat;
    case ArrUniqueStr;
    case ArrNotNull;
    case ArrValues;
}
