<?php

namespace Gbowo\Tests\Adapter\Paystack;

use Gbowo\Tests\Mockable;
use Gbowo\Adapter\Paystack\PaystackAdapter;
use Gbowo\Exception\InvalidHttpResponseException;

class PaystackAdapterTest extends \PHPUnit_Framework_TestCase
{

    use Mockable;

    public function testPaymentIsMade()
    {

        $mockedInterface = $this->getMockedResponseInterface();

        $data = ['data' => ['authorization_url' => 'ud']];

        $mockedInterface->shouldReceive('getBody')
            ->once()
            ->andReturn(json_encode($data));

        $mockedInterface->shouldReceive('getStatusCode')
            ->once()
            ->andReturn(200);

        $httpClient = $this->getMockedGuzzle();

        $httpClient->shouldReceive('post')
            ->once()
            ->andReturn($mockedInterface);

        $paystack = new PaystackAdapter($httpClient);

        $response = $paystack->charge(['amount' => 6000, 'blah' => 'blah']);

        $this->assertSame($data['data']['authorization_url'], $response);
    }


    /**
     * @expectedException \Gbowo\Exception\InvalidHttpResponseException
     */
    public function testReturnsInvalidHttpResponse()
    {

        $mockedInterface = $this->getMockedResponseInterface();

        $data = ['data' => ['authorization_url' => 'ud']];

        $mockedInterface->shouldReceive('getStatusCode')
            ->twice()
            ->andReturn(201);

        $httpClient = $this->getMockedGuzzle();

        $httpClient->shouldReceive('post')
            ->once()
            ->andReturn($mockedInterface);

        $paystack = new PaystackAdapter($httpClient);

        $paystack->charge(['amount' => 6000, 'blah' => 'blah']);


    }

}
