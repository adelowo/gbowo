<?php

namespace Gbowo;

if (!function_exists("Gbowo\\env")) {

    /**
     * * Load a value from `$_ENV`.
     * Cannot use `function_exists` check since it (`function_exists`) checks the global functions [not namespaced]
     * @param string $value
     * @return mixed
     */

    function env(string $value)
    {
        return $_ENV[$value];
    }
}

if (!function_exists("Gbowo\\generate_trans_ref")) {

    /**
     * Generate a cryptographically secure random string
     * @param int $length Defaults to 10
     * @return string
     */
    function generate_trans_ref(int $length = 10)
    {
        return bin2hex(random_bytes($length));
    }

}
