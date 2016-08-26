<?php

namespace Gbowo\Tests\Adapter\Paystack\Plugin;

use Mockery;
use DateTime;
use Gbowo\Tests\Mockable;
use Gbowo\Adapter\Paystack\PaystackAdapter;
use Gbowo\Adapter\Paystack\Plugin\GetAllTransactions;

class GetAllTransactionsTest extends \PHPUnit_Framework_TestCase
{
    use Mockable;

    public function testThePluginIsCalled()
    {

        $mockedInterface = $this->getMockedResponseInterface();

        $data = [
            'data' =>
                [
                    0 => [
                        "transaction_date" => (new DateTime("last week saturday"))->format("Y-m-d")
                    ],
                    1 => [
                        "transaction_date" => (new DateTime("yesterday"))->format("Y-m-d")
                    ],
                    2 => [
                        "transaction_date" => (new DateTime("today"))->format("Y-m-d")
                    ]
                ]
        ];

        $mockedInterface->shouldReceive('getBody')
            ->once()
            ->andReturn(json_encode($data));

        $httpClient = $this->getMockedGuzzle();

        $httpClient->shouldReceive('get')
            ->once()
            ->andReturn($mockedInterface);

        $paystack = new PaystackAdapter($httpClient);

        $paystack->addPlugin(new GetAllTransactions(PaystackAdapter::API_LINK));

        $returnedData = $paystack->getAllTransactions();

        $this->assertEquals($data['data'], $returnedData);

    }
}
