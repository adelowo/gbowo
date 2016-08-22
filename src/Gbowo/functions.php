<?php

namespace Gbowo;

if (!function_exists('env')) {

    /**
     * Load a value from `$_ENV`.
     * Bad thing is that it (`function_exists`) checks the global functions [not namespaced]
     * @param string $value
     * @return string|false
     */
    function env(string $value)
    {
        return getenv($value);
    }
}

if (!function_exists('genTransRef')) {

    /**
     * Generate a cryptographically secure random string
     * @param int $length Defaults to 10
     * @return string
     */
    function genTransRef(int $length = 10)
    {
        return bin2hex(random_bytes($length));
    }

}
