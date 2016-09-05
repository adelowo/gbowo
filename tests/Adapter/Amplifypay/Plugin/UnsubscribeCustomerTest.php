<?php

namespace Gbowo\Tests\Adapter\Amplifypay\Plugin;


use Gbowo\Tests\Mockable;
use Gbowo\Adapter\Amplifypay\AmplifypayAdapter;
use Gbowo\Adapter\Amplifypay\Plugin\UnsubscribeCustomer;
use Gbowo\Exception\InvalidHttpResponseException;
use Gbowo\Adapter\AmplifyPay\Exception\TransactionVerficationFailedException;

class UnsubscribeCustomerTest extends \PHPUnit_Framework_TestCase
{

    use Mockable;

    public function testPluginIsCalled()
    {

        $mockedResponse = $this->getMockedResponseInterface();

        $httpClient = $this->getMockedGuzzle();

        $httpClient->shouldReceive('post')
            ->once()
            ->andReturn($mockedResponse);

        $mockedResponse->shouldReceive("getStatusCode")
            ->once()
            ->andReturn(200);

        $response = [
            "apiKey" => \Gbowo\env("AMPLIFYPAY_API_KEY"),
            "StatusCode" => "000",
            "StatusDesc" => "Successfull Request"
        ];

        $mockedResponse->shouldReceive("getBody")
            ->once()
            ->andReturn(json_encode($response));


        $amplifyPay = new AmplifypayAdapter($httpClient);

        $data = [
            "transactionRef" => "7383",
            "customerEmail" => "easter@eggs.com",
            "planId" => "200",
        ];

        $res = $amplifyPay->unsubcribeCustomerFromPlan($data);

        $this->assertEquals($response['StatusDesc'], $res['StatusDesc']);
    }

    /**
     * @expectedException \Gbowo\Exception\InvalidHttpResponseException
     */
    public function testAnInvalidHttpStatusCodeIsReceived()
    {

        $mockedResponse = $this->getMockedResponseInterface();

        $httpClient = $this->getMockedGuzzle();

        $httpClient->shouldReceive('post')
            ->once()
            ->andReturn($mockedResponse);

        $mockedResponse->shouldReceive("getStatusCode")
            ->twice()
            ->andReturn(201);

        $response = [
            "apiKey" => \Gbowo\env("AMPLIFYPAY_API_KEY"),
            "StatusCode" => "000",
            "StatusDesc" => "Successfull Request"
        ];

        $mockedResponse->shouldReceive("getBody")
            ->never()
            ->andReturn(json_encode($response));


        $amplifyPay = new AmplifypayAdapter($httpClient);

        $data = [
            "transactionRef" => "7383",
            "customerEmail" => "easter@eggs.com",
            "planId" => "200",
        ];

        $amplifyPay->unsubcribeCustomerFromPlan($data);

    }

    /**
     * @expectedException \Gbowo\Adapter\AmplifyPay\Exception\TransactionVerficationFailedException
     */
    public function testTransactionFailsDueToWrongStatusDescription()
    {

        $mockedResponse = $this->getMockedResponseInterface();

        $httpClient = $this->getMockedGuzzle();

        $httpClient->shouldReceive('post')
            ->once()
            ->andReturn($mockedResponse);

        $mockedResponse->shouldReceive("getStatusCode")
            ->once()
            ->andReturn(200);

        $response = [
            "apiKey" => \Gbowo\env("AMPLIFYPAY_API_KEY"),
            "StatusCode" => "000",
            "StatusDesc" => "xSuccessfull Request"
        ];

        $mockedResponse->shouldReceive("getBody")
            ->once()
            ->andReturn(json_encode($response));

        $amplifyPay = new AmplifypayAdapter($httpClient);

        $data = [
            "transactionRef" => "7383",
            "customerEmail" => "easter@eggs.com",
            "planId" => "200",
        ];

        $amplifyPay->unsubcribeCustomerFromPlan($data);
    }
}
