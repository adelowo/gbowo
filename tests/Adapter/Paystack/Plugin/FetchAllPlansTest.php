<?php

namespace Adapter\Paystack\Plugin;

use Gbowo\Adapter\Paystack\PaystackAdapter;
use Gbowo\Adapter\Paystack\Plugin\FetchAllPlans;
use Gbowo\Tests\Mockable;
use PHPUnit\Framework\TestCase;

class FetchAllPlansTest extends TestCase
{
    use Mockable;


    /**
     * @dataProvider getAllPaystackPlans
     */
    public function testFetchAllPlansPluginIsCalled($allPlans)
    {
        $mockedResponseInterface = $this->getMockedResponseInterface();

        $httpClient = $this->getMockedGuzzle();

        $httpClient->shouldReceive('get')
            ->once()
            ->andReturn($mockedResponseInterface);

        $mockedResponseInterface->shouldReceive('getStatusCode')
            ->once()
            ->andReturn(200);

        $mockedResponseInterface->shouldReceive('getBody')
            ->once()
            ->andReturn(\GuzzleHttp\json_encode($allPlans));

        $paystack = (new PaystackAdapter($httpClient))->addPlugin(
            new FetchAllPlans(PaystackAdapter::API_LINK)
        );

        $data = $paystack->fetchAllPlans(["perPage" => 1, "amount" => 2200, "page" => 2]);

        $this->assertEquals($allPlans['data'], $data);
    }

    /**
     * @expectedException \Gbowo\Exception\InvalidHttpResponseException
     */
    public function testAnInvalidHttpExceptionIsReturned()
    {
        $httpClient = $this->getMockedGuzzle();

        $mockedResponseInterface = $this->getMockedResponseInterface();

        $httpClient->shouldReceive('get')
            ->once()
            ->andReturn($mockedResponseInterface);

        $mockedResponseInterface->shouldReceive('getStatusCode')
            ->once()
            ->andReturn(204);

        $paystack = (new PaystackAdapter($httpClient))->addPlugin(
            new FetchAllPlans(PaystackAdapter::API_LINK)
        );

        $paystack->fetchAllPlans();
    }

    public function getAllPaystackPlans()
    {
        $today = (new \DateTime())->format("Y-m-d");

        return [
            [
                "status" => true,
                "message" => "Plans Retrieved",
                "data" => [
                    "subscriptions" => [
                        "customer" => 63,
                        "plan" => 34,
                        "integration" => 2833,
                        "domain" => "test",
                        "status" => "complete",
                        "quantity" => 1,
                        "amount" => 2200,
                        "subscription_code" => "sbsdjsc_Scdc",
                        "email_code" => "djdbsjds_sdbshdw_De",
                        "authorization" => 623,
                        "next_payment_date" => (new \DateTime())->setTimestamp(strtotime("next year december"))->format("Y-m-d"),
                        "createdAt" => $today,
                        "updatedAt" => $today
                    ]
                ]
            ]
        ];
    }
}
