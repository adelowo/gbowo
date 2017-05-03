<?php

namespace Gbowo\Adapter\Paystack\Traits;

use Gbowo\Exception\InvalidHttpResponseException;
use Psr\Http\Message\ResponseInterface;

trait VerifyHttpStatusResponseCode
{
    protected function verifyResponse(ResponseInterface $response)
    {
        if (in_array($response->getStatusCode(), [200, 201], true)) {
            //https://developers.paystack.co/v1.0/docs/errors
            return;
        }

        throw InvalidHttpResponseException::createFromResponse($response);
    }
}
