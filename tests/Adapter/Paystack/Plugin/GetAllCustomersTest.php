<?php

namespace Gbowo\Tests\Adapter\Paystack\Plugin;

use Mockery;
use DateTime;
use Gbowo\Tests\Mockable;
use Gbowo\Adapter\Paystack\PaystackAdapter;
use Gbowo\Adapter\Paystack\Plugin\GetAllCustomers;

class GetAllCustomersTest extends \PHPUnit_Framework_TestCase
{
    use Mockable;

    public function testThePluginIsCalled()
    {

        $mockedInterface = $this->getMockedResponseInterface();

        $data = [
            'data' =>
                [
                    'id' => 123,
                    'authorization_code' => 'sss'
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

        $paystack->addPlugin(new GetAllCustomers(PaystackAdapter::API_LINK));

        $returnedData = $paystack->getAllCustomers();

        $this->assertEquals($data['data'], $returnedData);

    }


}
