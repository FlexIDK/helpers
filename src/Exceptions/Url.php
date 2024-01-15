<?php

namespace One23\Helpers\Exceptions;

use One23\Helpers\Exception as BaseException;

class Url extends BaseException
{
    const INVALID_VALUE = 1;

    const INVALID_URL = 20;

    const INVALID_URL_SCHEME_CHARTERS = 25;

    const INVALID_URL_HOST_IDN = 4;

    const INVALID_URL_HOST_IPV4 = 5;

    const INVALID_URL_HOST_IPV6 = 6;

    const INVALID_URL_HOST_LEVEL = 15;

    const INVALID_URL_HOST_CHARTERS = 26;

    const INVALID_URL_PORT = 7;

    const INVALID_URL_PATH = 10;

    const INVALID_URL_QUERY = 11;

    const INVALID_URL_FRAGMENT = 12;

    const UNDEFINED_SCHEME = 21;

    const UNDEFINED_HOST = 22;

    const DENY_IPV4 = 13;

    const DENY_IPV6 = 14;

    const DENY_WITH_PORT = 16;

    const DENY_WITHOUT_PORT = 17;

    const DENY_WITH_USER = 18;

    const DENY_WITHOUT_USER = 19;

    const DENY_SCHEME_HTTP = 23;

    const DENY_SCHEME_NOT_HTTP = 24;
}
