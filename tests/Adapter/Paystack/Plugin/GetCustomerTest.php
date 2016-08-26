<?php

namespace Gbowo\Tests\Adapter\Paystack\Plugin;

use Mockery;
use DateTime;
use Gbowo\Tests\Mockable;
use Gbowo\Adapter\Paystack\PaystackAdapter;
use Gbowo\Adapter\Paystack\Plugin\GetCustomer;

class GetCustomerTest extends \PHPUnit_Framework_TestCase
{

    use Mockable;

    public function testFindCustomerPluginIsCalled()
    {

        $mockedInterface = $this->getMockedResponseInterface();

        $data = [
            'data' =>
                [
                    'id' => 123,
                    'authorization_code' => 'sss',
                    'transaction_date' => (new DateTime())->format('m-y-j')
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

        $paystack->addPlugin(new GetCustomer(PaystackAdapter::API_LINK));

        $returnedData = $paystack->getCustomer(123);

        $this->assertEquals($data, $returnedData);

    }

}
