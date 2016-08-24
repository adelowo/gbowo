<?php

namespace Gbowo\Adapter\Amplifypay\Traits;

use Gbowo\Adapter\Amplifypay\Exception\KeyMismatchException;

/**
 * An Amplifypay best practice is to verify if the returned `ApiKey` in a response (HTTP 200) matches what was passed
 * to them.
 * @see https://amplifypay.com/developers Verifying transactions
 * @author Lanre Adelowo <me@adelowolanre.com>
 * Class KeyVerifier
 * @package Gbowo\Adapter\Amplifypay\Traits
 */
trait KeyVerifier
{

    /**
     * @param string $returnedKey
     * @param string $userKey
     * @throws \Gbowo\Adapter\Amplifypay\Exception\KeyMismatchException if the keys don't match
     */
    protected function verifyKeys(string $returnedKey, string $userKey)
    {
        if (strcmp($returnedKey, $userKey) !== 0) {
            throw new KeyMismatchException("Api keys don't match");
        }

        return;
    }
}
