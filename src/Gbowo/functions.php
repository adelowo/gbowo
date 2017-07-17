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

if (!function_exists("Gbowo\\toQueryParams")) {
    /**
     * Converts a dictionary into HTTP query parameter(s) which can then be attached to a link
     * ["name" => "Lanre", "hobby" => "Trolling"] gets formatted as ?name=lanre&hobby=trolling
     * @param  array $queryParams The dictionary to be converted
     * @return string A string that represents a "key-value" url query formed from a dictionary
     */
    function toQueryParams(array $queryParams = []) : string
    {
        $params = "";

        if (count($queryParams) == 0) {
            return $params;
        }

        foreach ($queryParams as $key => $value) {
            $encodedValue = urlencode($value);

            if (reset($queryParams) == $value) {
                $params .= "?{$key}={$encodedValue}";
            } else {
                $params .= "&{$key}={$encodedValue}";
            }
        }

        return $params;
    }
}
