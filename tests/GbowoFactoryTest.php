<?php

namespace Gbowo\Tests;

use Gbowo\Contract\Adapter\AdapterInterface;
use Gbowo\GbowoFactory;

class GbowoFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function getDefaultAdapters()
    {
        return [
            ["paystack"],
            ["amplifypay"]
        ];
    }

    /**
     * @dataProvider getDefaultAdapters
     */
    public function testAdapterCreation($adapter)
    {
        $this->assertInstanceOf(AdapterInterface::class, $this->factory()->createAdapter($adapter));
    }

    public function factory(array $adapters = [])
    {
        return new GbowoFactory($adapters);
    }

    public function getCustomAdapters()
    {

        $voguePay = new class implements AdapterInterface
        {
            protected $voguePayClient;

            public function __construct()
            {
                $this->voguePayClient = new \stdClass();
            }

            public function charge(array $data = [])
            {
                return "charged by voguepay";
            }
        };

        //Man can but to dream
        $interswitch = new class implements AdapterInterface
        {
            protected $interswitch;

            public function __construct()
            {
                $this->interswitch = new \stdClass(new \ArrayObject(new \stdClass())); // It wasn't me
            }

            public function charge(array $data = [])
            {
                return "charged by interswitch";
            }
        };

        return [
            [
                "voguepay" =>
                    [
                        "voguepay" => $voguePay,
                    ]
            ],
            [
                "interswitch" =>
                    [
                        "interswitch" => $interswitch,
                    ]
            ]
        ];
    }

    /**
     * @dataProvider getCustomAdapters
     */
    public function testAddedAdaptersAtRunTime($adapter)
    {
        $key = array_keys($adapter)[0];
        $factory = $this->factory($adapter);
        $this->assertInstanceOf(AdapterInterface::class, $factory->createAdapter($key));
    }

    /**
     * @expectedException \Gbowo\Exception\UnknownAdapterException
     */
    public function testUnknownAdapterIsRequested()
    {
        $this->factory()->createAdapter("stripe");
    }

    /**
     * @expectedException \Gbowo\Exception\UnknownAdapterException
     */
    public function testInvalidCustomAdapterIsMountedAtRuntime()
    {
        $custom = [
            "xxxx" => new \stdClass()
        ];

        $this->factory($custom)->createAdapter("xxxx");
    }

    /**
     * @expectedException \Exception
     */
    public function testInternalAdapterClassCannotBeOverridden()
    {
        $newPaystackAdapter = new class implements AdapterInterface
        {

            public function charge(array $data)
            {
                return "charged by new paystack adapter";
            }
        };

        $this->factory([GbowoFactory::PAYSTACK => $newPaystackAdapter])->createAdapter("paystack");
    }
}
