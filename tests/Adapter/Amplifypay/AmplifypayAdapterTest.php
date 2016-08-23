<?php

namespace Gbowo\Tests\Adapter\Amplifypay;

use Mockery\Mock;
use Gbowo\Tests\Mockable;
use Gbowo\Adapter\Amplifypay\AmplifypayAdapter;
use Gbowo\Exception\InvalidHttpResponseException;
use Gbowo\Adapter\Amplifypay\Exception\KeyMismatchException;

class AmplifypayAdapterTest extends \PHPUnit_Framework_TestCase
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
            "reference" => \Gbowo\genTransRef(),
            "redirectUrl" => "https://domainName.com/getdata",
            "paymentDescription" => "Testing The API"
        ];

        $response = $this->amplifyPay->charge($data);

        $this->assertSame($res['PaymentUrl'], $response);

    }

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
            "reference" => \Gbowo\genTransRef(),
            "redirectUrl" => "https://domainName.com/getdata",
            "paymentDescription" => "Testing The API"
        ];

        try {

            $response = $this->amplifyPay->charge($data);

        } catch (InvalidHttpResponseException $e) {

            $this->assertStringEndsWith(
                (string)$this->mockedResponseInterface->getStatusCode(),
                $e->getMessage()
            );
        }

    }

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
            "reference" => \Gbowo\genTransRef(),
            "redirectUrl" => "https://domainName.com/getdata",
            "paymentDescription" => "Testing The API"
        ];

        try {

            $response = $this->amplifyPay->charge($data);

        } catch (KeyMismatchException $e) {

            $this->assertEquals( $e->getMessage() , "Api keys don't match");

        }

    }

}

