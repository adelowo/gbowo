<?php

namespace Gbowo\Tests\Adapter\Amplifypay;

use Mockery\Mock;
use Gbowo\Tests\Mockable;
use Gbowo\Adapter\Amplifypay\AmplifypayAdapter;
use Gbowo\Exception\InvalidHttpResponseException;
use Gbowo\Adapter\Amplifypay\Exception\KeyMismatchException;
use PHPUnit\Framework\TestCase;

class AmplifypayAdapterTest extends TestCase
{

    use Mockable;

    /**
     * @var Mock
     */
    protected $mockedResponseInterface;

    /**
     * @var Mock
     */
    protected $httpClient;

    /**
     * @var AmplifypayAdapter
     */
    protected $amplifyPay;

    public function setUp()
    {
        $this->httpClient = $this->getMockedGuzzle();
        $this->mockedResponseInterface = $this->getMockedResponseInterface();

        $this->amplifyPay = new AmplifypayAdapter($this->httpClient);
    }

    public function testPaymentIsMade()
    {

        $this->mockedResponseInterface->shouldReceive('getStatusCode')
            ->once()
            ->andReturn(200);

        $res = [
            "ApiKey" => \Gbowo\env("AMPLIFYPAY_API_KEY"),
            "StatusCode" => "200",
            "StatusDesc" => "Successful Request",
            "TransID" => "2468563223",
            "TransactionRef" => "1234567890",
            "AuthToken" => "xxxxxx",
            "PaymentUrl" => "https://amplifypay.com/gateway/xxx",
        ];

        $this->mockedResponseInterface->shouldReceive('getBody')
            ->once()
            ->andReturn(json_encode($res));

        $this->httpClient->shouldReceive('post')
            ->once()
            ->andReturn($this->mockedResponseInterface);

        $data = [
            "customerEmail" => "xx@adelowla.com",
            "customerName" => "Lanre",
            "Amount" => 4000.00,
            "reference" => \Gbowo\generateTransRef(),
            "redirectUrl" => "https://domainName.com/getdata",
            "paymentDescription" => "Testing The API"
        ];

        $response = $this->amplifyPay->charge($data);

        $this->assertSame($res['PaymentUrl'], $response);

    }

    /**
     * @expectedException \Gbowo\Exception\InvalidHttpResponseException
     */
    public function testApiReturnsInvalidHttpStatusCodeAndAnExceptionIsThrown()
    {

        $this->mockedResponseInterface->shouldReceive('getStatusCode')
            ->between(1, 3)
            ->andReturn(202);

        $res = [
            "ApiKey" => \Gbowo\env("AMPLIFYPAY_API_KEY"),
            "StatusCode" => "200",
            "StatusDesc" => "Successful Request",
            "TransID" => "2468563223",
            "TransactionRef" => "1234567890",
            "AuthToken" => "xxxxxx",
            "PaymentUrl" => "https://amplifypay.com/gateway/xxx",
        ];

        $this->mockedResponseInterface->shouldReceive('getBody')
            ->never()
            ->andReturn(json_encode($res));

        $this->httpClient->shouldReceive('post')
            ->once()
            ->andReturn($this->mockedResponseInterface);

        $data = [
            "customerEmail" => "xx@adelowla.com",
            "customerName" => "Lanre",
            "Amount" => 4000.00,
            "reference" => \Gbowo\generateTransRef(),
            "redirectUrl" => "https://domainName.com/getdata",
            "paymentDescription" => "Testing The API"
        ];

        $this->amplifyPay->charge($data);

    }

    /**
     * @expectedException \Gbowo\Adapter\AmplifyPay\Exception\KeyMismatchException
     */
    public function testRequestAndResponseApiKeysMustBeTheSame()
    {

        $this->mockedResponseInterface->shouldReceive('getStatusCode')
            ->once()
            ->andReturn(200);

        $res = [
            "ApiKey" => \Gbowo\env("AMPLIFYPAY_API_KEY") . 'xx', //alter the returned key
            "StatusCode" => "200",
            "StatusDesc" => "Successful Request",
            "TransID" => "2468563223",
            "TransactionRef" => "1234567890",
            "AuthToken" => "xxxxxx",
            "PaymentUrl" => "https://amplifypay.com/gateway/xxx",
        ];

        $this->mockedResponseInterface->shouldReceive('getBody')
            ->once()
            ->andReturn(json_encode($res));

        $this->httpClient->shouldReceive('post')
            ->once()
            ->andReturn($this->mockedResponseInterface);

        $data = [
            "customerEmail" => "xx@adelowla.com",
            "customerName" => "Lanre",
            "Amount" => 4000.00,
            "reference" => \Gbowo\generateTransRef(),
            "redirectUrl" => "https://domainName.com/getdata",
            "paymentDescription" => "Testing The API"
        ];

        $this->amplifyPay->charge($data);

    }

}
