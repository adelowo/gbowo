<?php

namespace Adapter\Amplifypay\Plugin;

use Gbowo\Adapter\Amplifypay\AmplifypayAdapter;
use Gbowo\Adapter\Amplifypay\Plugin\FetchAllPlans;
use Gbowo\Tests\Mockable;
use function Gbowo\env;
use PHPUnit\Framework\TestCase;

class FetchAllPlansTest extends TestCase
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
     * @param $response
     * @dataProvider getAllPlans
     */
    public function testFetchAllPlansPluginIsCalled($response)
    {
        $mockedResponse = $this->getMockedResponseInterface();

        $httpClient = $this->getMockedGuzzle();

        $httpClient->shouldReceive('get')
            ->once()
            ->andReturn($mockedResponse);

        $mockedResponse->shouldReceive('getStatusCode')
            ->once()
            ->andReturn(200);

        $mockedResponse->shouldReceive('getBody')
            ->once()
            ->andReturn($response);


        $amplifyPay = (new AmplifypayAdapter($httpClient))->addPlugin(
            new FetchAllPlans(AmplifypayAdapter::BASE_URL, $this->apiKeys)
        );

        $receivedResponse = $amplifyPay->fetchAllPlans();

        $this->assertEquals($response, $receivedResponse);
    }


    /**
     * @expectedException \Gbowo\Exception\InvalidHttpResponseException
     */
    public function testAnInvalidHttpResponseIsReceived()
    {
        $mockedResponse = $this->getMockedResponseInterface();

        $httpClient = $this->getMockedGuzzle();


        $httpClient->shouldReceive('get')
            ->once()
            ->andReturn($mockedResponse);

        $mockedResponse->shouldReceive('getStatusCode')
            ->once()
            ->andReturn(201);

        $mockedResponse->shouldReceive('getBody')
            ->never()
            ->andReturnNull();

        $amplifyPay = (new AmplifypayAdapter($httpClient))->addPlugin(
            new FetchAllPlans(AmplifypayAdapter::BASE_URL, $this->apiKeys)
        );

        $receivedResponse = $amplifyPay->fetchAllPlans();
    }


    public function getAllPlans()
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
            ],
            [
                "PlanId" => 234,
                "PlanName" => "Silver Guyz",
                "Frequency" => "Monthly",
                "DateCreated" => (new \DateTime())->setTimestamp(strtotime("13 years ago"))->format("Y-m-d")
            ],
            [
                "PlanId" => 432,
                "PlanName" => "Regular Homies",
                "Frequency" => "Yearly",
                "DateCreated" => (new \DateTime())->setTimestamp(strtotime("last year"))->format("Y-m-d")
            ]
        ];
    }
}
