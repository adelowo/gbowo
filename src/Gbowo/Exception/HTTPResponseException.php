<?php

namespace Gbowo\Exception;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Gbowo\Contract\Exception\ResponseExceptionInterface;

abstract class HTTPResponseException extends Exception implements ResponseExceptionInterface
{
    protected $response;

    public static function createFromResponse(
        ResponseInterface $response
    ) : Exception {
        $ret = new static();

        $ret->setResponse($response);

        return $ret;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
    }
}
