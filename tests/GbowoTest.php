<?php

namespace Gbowo\Tests;

use Gbowo\Gbowo;
use LogicException;
use Gbowo\Adapter\Paystack\PaystackAdapter;
use Gbowo\Tests\Fixtures\UnhandleablePlugin;
use Gbowo\Exception\PluginNotFoundException;
use Gbowo\Adapter\Paystack\Plugin\GetPaymentData;

class GbowoTest extends \PHPUnit_Framework_TestCase
{

    use Mockable;

    public function testPluginNotFound()
    {

        $httpClient = $this->getMockedGuzzle();

        $paystack = new PaystackAdapter($httpClient);

        $gbowo = new Gbowo($paystack);

        try {

            $gbowo->unknownPlugin(10);

        } catch (PluginNotFoundException $e) {

            $this->assertEquals("Plugin with accessor, unknownPlugin not found", $e->getMessage());
        }
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage is not a callable plugin as it does not have an handle method
     */
    public function testPluginCannotBeHandled()
    {
        $httpClient = $this->getMockedGuzzle();

        $paystack = new PaystackAdapter($httpClient);

        $paystack->addPlugin(new UnhandleablePlugin());

        $gbowo = new Gbowo($paystack);

        $gbowo->unhandleable(10);

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

}
