<?php

namespace Gbowo\Tests\Adapter\Amplifypay\Plugin;

use Gbowo\Tests\Mockable;
use function Gbowo\env;
use Gbowo\Adapter\Amplifypay\AmplifypayAdapter;
use Gbowo\Adapter\Amplifypay\Plugin\ChargeWithToken;
use Gbowo\Exception\TransactionVerficationFailedException;
use PHPUnit\Framework\TestCase;

class ChargeWithTokenTest extends TestCase
{
    use Mockable;

    public function testChargeWithTokenPluginIsCalled()
    {
        $data = [
            'me' => "you",
            "StatusDesc" => ChargeWithToken::SUCCESSFUL_TRANSACTION,
            "apiKey" => env("AMPLIFYPAY_API_KEY")
        ];

        $httpclient = $this->getMockedGuzzle();

        $response = $this->getMockedResponseInterface();

        $httpclient->shouldReceive("post")
            ->once()
            ->andReturn($response);

        $response->shouldReceive("getStatusCode")
            ->once()
            ->andReturn(200);

        $response->shouldReceive("getBody")
            ->once()
            ->andReturn(json_encode($data));


        $adapter = new AmplifypayAdapter($httpclient);

        $res = $adapter->chargeWithToken(["transactionRef" => 333, "authCode" => 3]);

        $this->assertSame($data, $res);
    }


    /**
     * @expectedException \Gbowo\Exception\InvalidHttpResponseException
     */
    public function testAnInvalidStatusCodeIsReceived()
    {
        $httpclient = $this->getMockedGuzzle();

        $response = $this->getMockedResponseInterface();

        $httpclient->shouldReceive("post")
            ->once()
            ->andReturn($response);

        $response->shouldReceive("getStatusCode")
            ->twice()
            ->andReturn(201);

        $response->shouldReceive("getBody")
            ->never()
            ->andReturn(json_encode(null));


        $adapter = new AmplifypayAdapter($httpclient);

        $adapter->chargeWithToken(["transactionRef" => 333, "authCode" => 3]);
    }


    /**
     * @expectedException \Gbowo\Exception\TransactionVerficationFailedException
     */
    public function testAnInvalidStatusDescriptionIsReceived()
    {
        $httpclient = $this->getMockedGuzzle();

        $response = $this->getMockedResponseInterface();

        $httpclient->shouldReceive("post")
            ->once()
            ->andReturn($response);

        $response->shouldReceive("getStatusCode")
            ->once()
            ->andReturn(200);

        $response->shouldReceive("getBody")
            ->once()
            ->andReturn(
                json_encode(
                    [
                        'me' => "you",
                        "StatusDesc" => "Could not verify that the customer was unsubscribed",
                        "apiKey" => env("AMPLIFYPAY_API_KEY")
                    ]
                )
            );


        $adapter = new AmplifypayAdapter($httpclient);

        $adapter->chargeWithToken(["transactionRef" => 333, "authCode" => 3]);
    }
}
