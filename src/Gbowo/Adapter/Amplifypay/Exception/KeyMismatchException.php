<?php

namespace Gbowo\Adapter\Amplifypay\Exception;

use Exception;

/**
 * Throw an exception if the key used to initiate a transaction does not match what Amplifypay returns.
 * Key verification is an AmplifyPay best practice
 * @see https://amplifypay.com/developers Verifying transactions
 * @author Lanre Adelowo <me@adelowolanre.com>
 * Class KeyMismatchException
 * @package Gbowo\Adapter\Amplifypay\Exception
 */
class KeyMismatchException extends Exception
{
}