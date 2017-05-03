<?php

namespace Gbowo\Contract\Exception;

use Exception;
use Psr\Http\Message\ResponseInterface;

interface ResponseExceptionInterface
{
    public function getResponse(): ResponseInterface;

    public function setResponse(ResponseInterface $response);

    public static function createFromResponse(ResponseInterface $response): Exception;
}
