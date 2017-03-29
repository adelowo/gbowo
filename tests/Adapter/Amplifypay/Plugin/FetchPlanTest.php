<?php

namespace Adapter\Amplifypay\Plugin;

use function Gbowo\env;
use Gbowo\Tests\Mockable;
use Gbowo\Adapter\Amplifypay\Plugin\FetchPlan;
use Gbowo\Adapter\Amplifypay\AmplifypayAdapter;
use PHPUnit\Framework\TestCase;

class FetchPlanTest extends TestCase
{

    use Mockable;

    /**
     * @var array
     */
    protected $apiKeys;

    public function setUp()
    {
        $this->apiKeys = [
            "merchantId" => env("AMPLIFYPAY_MERCHANT_ID"),
            "apiKey" => env("AMPLIFYPAY_API_KEY")
        ];
    }

    /**
     * @dataProvider getSuccessfulResponse
     */
    public function testFetchPlanPluginIsCalled($response)
    {

        $mockedResponse = $this->getMockedResponseInterface();

        $httpClient = $this->getMockedGuzzle();

        $httpClient->shouldReceive('get')
            ->once()
            ->andReturn($mockedResponse);


        $mockedResponse->shouldReceive('getStatusCode')
            ->once()
            ->withNoArgs()
            ->andReturn(200);

        $mockedResponse->shouldReceive('getBody')
            ->once()
            ->withNoArgs()
            ->andReturn(\GuzzleHttp\json_encode($response));


        $amplifyPay = (new AmplifypayAdapter($httpClient))->addPlugin(
            new FetchPlan(AmplifypayAdapter::BASE_URL, $this->apiKeys)
        );


        $receivedResponse = $amplifyPay->fetchPlan(1);

        $this->assertEquals($response, $receivedResponse);
    }

    /**
     * @expectedException \Gbowo\Exception\InvalidHttpResponseException
     * @expectedExceptionMessage  Expected 200
     */
    public function testAnInvalidHttpStatusCodeIsReceived()
    {
        $mockedResponse = $this->getMockedResponseInterface();

        $httpClient = $this->getMockedGuzzle();


        $httpClient->shouldReceive('get')
            ->once()
            ->andReturn($mockedResponse);

        $mockedResponse->shouldReceive('getStatusCode')
            ->twice()
            ->withNoArgs()
            ->andReturn(201);

        $mockedResponse->shouldReceive('getBody')
            ->never()
            ->withNoArgs()
            ->andReturnNull();

        $amplifyPay = (new AmplifypayAdapter($httpClient))->addPlugin(
            new FetchPlan(AmplifypayAdapter::BASE_URL, $this->apiKeys)
        );


        $receivedResponse = $amplifyPay->fetchPlan(1);

    }

    public function getSuccessfulResponse()
    {
        return [
            [
                "PlanId" => 1,
                "PlanName" => "Platinum Dudes",
                "Frequency" => "Custom",
                "Params" => [
                    "CustomNum" => 7,
                    "CustomDay" => "Day"
                ],
                "DateCreated" => (new \DateTime())->setTimestamp(strtotime("20 years ago"))->format("Y-m-d")
            ]
//            ],
//            [
//                "PlanId" => 234,
//                "PlanName" => "Silver Guyz",
//                "Frequency" => "Monthly",
//                "DateCreated" => (new \DateTime())->setTimestamp(strtotime("13 years ago"))->format("Y-m-d")
//            ],
//            [
//                "PlanId" => 432,
//                "PlanName" => "Regular Homies",
//                "Frequency" => "Yearly",
//                "DateCreated" => (new \DateTime())->setTimestamp(strtotime("last year"))->format("Y-m-d")
//            ]
        ];
    }
}
