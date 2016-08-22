<?php

namespace Gbowo\Tests\Adapter\Paystack;

use Gbowo\Exception\InvalidHttpResponseException;
use Gbowo\Tests\Mockable;
use Gbowo\Adapter\Paystack\PaystackAdapter;

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

        try {

            $response = $paystack->charge(['amount' => 6000, 'blah' => 'blah']);

        } catch (InvalidHttpResponseException $e) {

            $this->assertStringEndsWith("201",$e->getMessage());
        }

    }

}
