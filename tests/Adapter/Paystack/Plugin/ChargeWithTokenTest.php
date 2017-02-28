<?php

namespace Adapter\Paystack\Plugin;

use Gbowo\Tests\Mockable;
use Gbowo\Adapter\Paystack\PaystackAdapter;
use Gbowo\Adapter\Paystack\Plugin\ChargeWithToken;
use Gbowo\Adapter\Paystack\Exception\TransactionVerficationFailedException;

class ChargeWithTokenTest extends \PHPUnit_Framework_TestCase
{

    use Mockable;

    public function testChargeWithTokenPluginIsCalled()
    {
        $mockedResonse = $this->getMockedResponseInterface();

        $httpClient = $this->getMockedGuzzle();

        $data = [
            "message" => ChargeWithToken::SUCCESS_MESSAGE,
            "data" => [
                "gateway_response" => ChargeWithToken::SUCCESS_MESSAGE,
                "amount" => 4000,
                "transaction_date" => (new \DateTime("today"))->format("Y-m-d"),
                "status" => "success",
                "reference" => \Gbowo\generateTransRef()
            ]
        ];

        $httpClient->shouldReceive('post')
            ->once()
            ->andReturn($mockedResonse);

        $mockedResonse->shouldReceive('getBody')
            ->once()
            ->andReturn(\GuzzleHttp\json_encode($data));

        $mockedResonse->shouldReceive("getStatusCode")
            ->once()
            ->andReturn(200);

        $paystackAdapter = new PaystackAdapter($httpClient);

        $paystackAdapter->addPlugin(new ChargeWithToken(PaystackAdapter::API_LINK));

        $response = $paystackAdapter->chargeWithToken(
            [
                "token" => "ballss2-3",
                "amount" => \Gbowo\toKobo(40),
                "email" => "me@adelowolanre.com"
            ]
        );

        $this->assertEquals($data['data'], $response);
    }

    /**
     * @expectedException \Gbowo\Adapter\Paystack\Exception\TransactionVerficationFailedException
     */
    public function testInvalidTransactionMessageIsReturned()
    {
        $mockedResponse = $this->getMockedResponseInterface();

        $data = [
            "message" => "Unknown message",
            "data" => [
                "amount" => 4000,
                "transaction_date" => (new \DateTime("today"))->format("Y-m-d"),
                "status" => "success",
                "reference" => \Gbowo\generateTransRef(),
                "gateway_response" => "bad"
            ]
        ];

        $mockedResponse->shouldReceive('getBody')
            ->once()
            ->andReturn(\GuzzleHttp\json_encode($data));

        $mockedResponse->shouldReceive("getStatusCode")
            ->once()
            ->andReturn(200); //a valid response code but still fails since the response wasn't expected

        $httpClient = $this->getMockedGuzzle();

        $httpClient->shouldReceive('post')
            ->once()
            ->andReturn($mockedResponse);


        $paystack = (new PaystackAdapter($httpClient))
            ->addPlugin(new ChargeWithToken(PaystackAdapter::API_LINK));

        $paystack->chargeWithToken(["token" => "dddd", "email" => "me@adelowolanre.com"]);
    }

    /**
     * @expectedException \Gbowo\Exception\InvalidHttpResponseException
     */
    public function testAnInvalidHttpResponseCodeIsReceived()
    {
        $mockedResponse = $this->getMockedResponseInterface();

        $data = [
            "message" => "Successful",
            "data" => [
                "amount" => 4000,
                "transaction_date" => (new \DateTime("today"))->format("Y-m-d"),
                "status" => "success",
                "reference" => \Gbowo\generateTransRef(),
                "gateway_response" => "bad"
            ]
        ];

        $mockedResponse->shouldReceive('getBody')
            ->never()
            ->andReturnNull();

        $mockedResponse->shouldReceive("getStatusCode")
            ->twice()
            ->andReturn(204);

        $httpClient = $this->getMockedGuzzle();

        $httpClient->shouldReceive('post')
            ->once()
            ->andReturn($mockedResponse);


        $paystack = (new PaystackAdapter($httpClient))
            ->addPlugin(new ChargeWithToken(PaystackAdapter::API_LINK));

        $paystack->chargeWithToken(["token" => "dddd", "email" => "me@adelowolanre.com"]);
    }
}
