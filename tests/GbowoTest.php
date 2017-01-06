<?php

namespace Gbowo\Tests;

use Gbowo\Gbowo;
use Gbowo\Plugin\AbstractPlugin;
use Gbowo\Adapter\Paystack\PaystackAdapter;
use Gbowo\Adapter\Paystack\Plugin\GetPaymentData;

class GbowoTest extends \PHPUnit_Framework_TestCase
{

    use Mockable;

    /**
     * @expectedException \Gbowo\Exception\PluginNotFoundException
     * @expectedExceptionMessage Plugin with accessor
     */
    public function testPluginNotFound()
    {

        $httpClient = $this->getMockedGuzzle();

        $paystack = new PaystackAdapter($httpClient);

        $gbowo = new Gbowo($paystack);


        $gbowo->unknownPlugin(10);

    }

    public function testGbowoCallsAPluginOnTheAdapterInUseAndChargesTheCustomer()
    {

        $httpClient = $this->getMockedGuzzle();
        $mockedInterface = $this->getMockedResponseInterface();

        $data = [
            "body" => true,
            "message" => GetPaymentData::VERIFIED_TRANSACTION,
            "data" => [
                "authorization_url" => "https://paystack.co/secure/xxx-movie"
            ]
        ];

        $mockedInterface->shouldReceive('getBody')
            ->twice()
            ->andReturn(json_encode($data));

        $httpClient->shouldReceive('get')
            ->once()
            ->andReturn($mockedInterface);

        $mockedInterface->shouldReceive('getStatusCode')
            ->once()
            ->andReturn(200);

        $httpClient->shouldReceive('post')
            ->once()
            ->andReturn($mockedInterface);


        $paystack = new PaystackAdapter($httpClient);

        $gbowo = new Gbowo($paystack);

        $gbowo->getPaymentData('245');

        $gbowo->charge(['amount' => 6000, 'blah' => 'blah']);

        $this->assertInstanceOf(get_class($paystack), $gbowo->getPaymentAdapter());

    }

    /**
     * @see Gbowo::__call
     * This also show multiple args can still be accepted if a plugin wishes
     */
    public function testPluginExpectsMultipleArgs()
    {

        $stub = ["dummy", ["token" => 111]];

        $dummy = new class extends AbstractPlugin
        {

            protected $baseUrl;

            public function getPluginAccessor() : string
            {
                return 'dummy';
            }

            public function handle(...$args)
            {
                return $args;
            }
        };

        $paystack = new PaystackAdapter();

        $paystack->addPlugin($dummy);

        $gbowo = new Gbowo($paystack);

        $response = $gbowo->dummy($stub[0], $stub[1]);

        $this->assertEquals($stub, $response);

    }
}
