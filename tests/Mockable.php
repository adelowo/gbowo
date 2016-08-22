<?php

namespace Gbowo\Tests;

use Mockery;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

/**
 * Partial mocks alone and a central `tearDown` method for destroying the Mock container
 */
trait Mockable
{

    public function tearDown()
    {
        Mockery::close();
    }

    protected function getMockedGuzzle()
    {
        return Mockery::mock(Client::class)->makePartial();
    }

    protected function getMockedResponseInterface()
    {
        return Mockery::mock(ResponseInterface::class)->makePartial();
    }


}
