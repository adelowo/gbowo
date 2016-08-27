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
                "amount" => 4000,
                "transaction_date" => (new \DateTime("today"))->format("Y-m-d"),
                "status" => "success",
                "reference" => \Gbowo\generate_trans_ref()
            ]
        ];

        $mockedResonse->shouldReceive('getBody')
            ->once()
            ->andReturn(\GuzzleHttp\json_encode($data));

        $httpClient->shouldReceive('post')
            ->once()
            ->andReturn($mockedResonse);

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
     * @expectedException \InvalidArgumentException
     */
    public function testUserTokenNotProvided()
    {
        $mockedResponse = $this->getMockedResponseInterface();

        $mockedResponse->shouldReceive('getBody')
            ->never()
            ->andReturnSelf();

        $httpclient = $this->getMockedGuzzle();
        $httpclient->shouldReceive('post')
            ->never()
            ->andReturnSelf();

        $paystack = new PaystackAdapter($httpclient);

        $paystack->addPlugin(new ChargeWithToken(PaystackAdapter::API_LINK));

        $paystack->chargeWithToken(["email" => "me@adelowolanre.com", "bool" => true]);
    }

    public function testInvalidTransactionMessageIsReturned()
    {
        $mockedResponse = $this->getMockedResponseInterface();

        $data = [
            "message" => "Unknown message",
            "data" => [
                "amount" => 4000,
                "transaction_date" => (new \DateTime("today"))->format("Y-m-d"),
                "status" => "success",
                "reference" => \Gbowo\generate_trans_ref()
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

        try {
            $paystack->chargeWithToken(["token" => "dddd", "email" => "me@adelowolanre.com"]);
        } catch (TransactionVerficationFailedException $e) {

        }
    }
}
