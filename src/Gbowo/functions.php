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
