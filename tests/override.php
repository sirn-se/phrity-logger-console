<?php

namespace Phrity\Logger\Console;

/*
 * File used to override core php functions, needed for test cases.
 */

function php_sapi_name(): string
{
    if (isset($GLOBALS['override_php_sapi_name'])) {
        return $GLOBALS['override_php_sapi_name'];
    }
    return \php_sapi_name();
}

function defined(string $name): bool
{
    if (isset($GLOBALS['override_defined'])) {
        return $GLOBALS['override_defined'];
    }
    return \defined($name);
}
