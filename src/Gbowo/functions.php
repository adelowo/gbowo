<?php

namespace Gbowo;

if (!function_exists("Gbowo\\env")) {
    /**
     * * Load a value from `$_ENV`.
     * @param string $value
     * @return mixed
     */
    function env(string $value)
    {
        return $_ENV[$value];
    }
}

if (!function_exists("Gbowo\\generateTransRef")) {
    /**
     * Generate a cryptographically secure random string
     * @param int $length Defaults to 10
     * @return string
     */
    function generateTransRef(int $length = 10)
    {
        return bin2hex(random_bytes($length));
    }
}

if (!function_exists("Gbowo\\toKobo")) {
    /**
     * Convert a given amount to it's kobo equivalent.
     * This is just an helper function and you def' can do without it
     * @param  $amount
     * @return float
     */
    function toKobo($amount)
    {
        return $amount * 100;
    }
}
