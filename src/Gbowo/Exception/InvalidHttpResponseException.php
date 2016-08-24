<?php

namespace Gbowo\Exception;

use Exception;

/**
 * Throw this if you really want to make sure you get a 200 HTTP response status code.
 * After all, it's payment and you just cannot trust nothing
 * @author Lanre Adelowo <me@adelowolanre.com>
 * Class InvalidHttpResponseException
 * @package Gbowo\Exception
 */
class InvalidHttpResponseException extends Exception
{

}
