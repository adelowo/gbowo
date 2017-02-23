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

        $paystackAdapter = new PaystackAdapter($httpClient);

        $paystackAdapter->addPlugin(new ChargeWithToken(PaystackAdapter::API_LINK));

        $response = $paystackAdapter->chargeWithToken(
            [
                "token" => "ballss2-3",
                "amount" => 4000,
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

        $httpClient = $this->getMockedGuzzle();

        $httpClient->shouldReceive('post')
            ->once()
            ->andReturn($mockedResponse);


        $paystack = (new PaystackAdapter($httpClient))
            ->addPlugin(new ChargeWithToken(PaystackAdapter::API_LINK));

        $paystack->chargeWithToken(["token" => "dddd", "email" => "me@adelowolanre.com"]);
    }
}
