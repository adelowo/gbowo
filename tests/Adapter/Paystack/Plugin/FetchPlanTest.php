<?php


namespace Adapter\Paystack\Plugin;


use Gbowo\Adapter\Paystack\PaystackAdapter;
use Gbowo\Adapter\Paystack\Plugin\FetchPlan;
use Gbowo\Tests\Mockable;

class FetchPlanTest extends \PHPUnit_Framework_TestCase
{

    use Mockable;

    /**
     * @var array
     */
    protected $response;

    /**
     * @dataProvider getSuccessfulResponse
     */
    public function testFetchPlanPluginIsCalled($response)
    {
        $httpClient = $this->getMockedGuzzle();

        $mockedResponseInterface = $this->getMockedResponseInterface();

        $httpClient->shouldReceive('get')
            ->once()
            ->andReturn($mockedResponseInterface);

        $mockedResponseInterface->shouldReceive('getStatusCode')
            ->once()
            ->withNoArgs()
            ->andReturn(200);

        $mockedResponseInterface->shouldReceive('getBody')
            ->once()
            ->andReturn(\GuzzleHttp\json_encode($response));

        $paystack = (new PaystackAdapter($httpClient))->addPlugin(new FetchPlan(PaystackAdapter::API_LINK));

        $data = $paystack->fetchPlan("jdde_33dswd");

        $this->assertEquals($data, $response['data']);
    }


    /**
     * @expectedException \Gbowo\Exception\InvalidHttpResponseException
     * @expectedExceptionMessage Expected 200
     */
    public function testApiReturnsAnInvalidHttpException()
    {
        $httpClient = $this->getMockedGuzzle();

        $mockedResponseInterface = $this->getMockedResponseInterface();

        $httpClient->shouldReceive('get')
            ->once()
            ->andReturn($mockedResponseInterface);

        $mockedResponseInterface->shouldReceive('getStatusCode')
            ->twice()
            ->withNoArgs()
            ->andReturn(201);

        $mockedResponseInterface->shouldReceive('getBody')
            ->never()
            ->andReturn(\GuzzleHttp\json_encode([]));

        $paystack = (new PaystackAdapter($httpClient))->addPlugin(new FetchPlan(PaystackAdapter::API_LINK));

        $response = $paystack->fetchPlan("xxxxxxxx");

    }

    public function getSuccessfulResponse()
    {
        $date = (new \DateTime())->format('Y-m-d');

        return [
            [
                "status" => true,
                "message" => "Plan retrieved",
                "data" => [
                    "currency" => "NGN",
                    "id" => 34,
                    "subscription" => [],
                    "integration" => 363,
                    "domain" => "test",
                    "name" => "xxx monthly stash",
                    "plan_code" => "jdde_33dswd",
                    "description" => "sdd",
                    "createdAt" => $date,
                    "updatedAt" => $date
                ]
            ]
        ];
    }
}
