<?php

namespace Gbowo\Tests\Adapter\Amplifypay\Plugin;

use Mockery\Mock;
use Gbowo\Tests\Mockable;
use Gbowo\Adapter\Amplifypay\AmplifypayAdapter;
use Gbowo\Exception\InvalidHttpResponseException;
use Gbowo\Adapter\Amplifypay\Plugin\GetPaymentData;
use Gbowo\Adapter\AmplifyPay\Exception\TransactionVerficationFailedException;
use PHPUnit\Framework\TestCase;

class GetPaymentDataTest extends TestCase
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


    public function testGetPaymentPluginIsCalled()
    {

        $res = [
            "ApiKey" => \Gbowo\env("AMPLIFYPAY_API_KEY"),
            "TransID" => "2468563223",
            "StatusCode" => "001",
            "StatusDesc" => "Approved",
            "OrderStatus" => "APPROVED",
            "TransactionRef" => "12345",
            "Amount" => "100.0000",
            "CardNumber" => "412345XXXX1234",
            "CardType" => "VISA",
            "AuthCode" => "567890 A",
            "Currency" => "Naira",
            "TranDate" => "01/01/2013 15:20:18",
            "CustomerName" => "Mr Tester",
            "CustomerEmail" => "test@tester.com"
        ];

        $this->mockedResponseInterface->shouldReceive('getStatusCode')
            ->once()
            ->andReturn(200);

        $this->mockedResponseInterface->shouldReceive('getBody')
            ->once()
            ->andReturn(json_encode($res));

        $this->httpClient->shouldReceive('get')
            ->once()
            ->andReturn($this->mockedResponseInterface);

        $returnedData = $this->amplifyPay->getPaymentData('s');

        $this->assertEquals($res, $returnedData);
    }


    /**
     * @expectedException \Gbowo\Exception\InvalidHttpResponseException
     */
    public function testInvalidHttpResponseIsReturned()
    {

        $res = [
            "ApiKey" => \Gbowo\env("AMPLIFYPAY_API_KEY"),
            "TransID" => "2468563223",
            "StatusCode" => "001",
            "StatusDesc" => "Approved",
            "OrderStatus" => "APPROVED",
            "TransactionRef" => "12345",
            "Amount" => "100.0000",
            "CardNumber" => "412345XXXX1234",
            "CardType" => "VISA",
            "AuthCode" => "567890 A",
            "Currency" => "Naira",
            "TranDate" => "01/01/2013 15:20:18",
            "CustomerName" => "Mr Tester",
            "CustomerEmail" => "test@tester.com"
        ];

        $this->mockedResponseInterface->shouldReceive('getStatusCode')
            ->twice()
            ->andReturn(201);

        $this->mockedResponseInterface->shouldReceive('getBody')
            ->once()
            ->andReturn(json_encode($res));

        $this->httpClient->shouldReceive('get')
            ->once()
            ->andReturn($this->mockedResponseInterface);

        $returnedData = $this->amplifyPay->getPaymentData('s');


    }

    /**
     * @expectedException \Gbowo\Adapter\AmplifyPay\Exception\TransactionVerficationFailedException
     */
    public function testTransactionFailsBecauseRequestWasNotAccepted()
    {
        $res = [
            "ApiKey" => \Gbowo\env("AMPLIFYPAY_API_KEY"),
            "TransID" => "2468563223",
            "StatusCode" => "001",
            "StatusDesc" => "Approvedx",
            "OrderStatus" => "APPROVEDx",
            "TransactionRef" => "12345",
            "Amount" => "100.0000",
            "CardNumber" => "412345XXXX1234",
            "CardType" => "VISA",
            "AuthCode" => "567890 A",
            "Currency" => "Naira",
            "TranDate" => "01/01/2013 15:20:18",
            "CustomerName" => "Mr Tester",
            "CustomerEmail" => "test@tester.com"
        ];

        $this->mockedResponseInterface->shouldReceive('getStatusCode')
            ->once()
            ->andReturn(200);

        $this->mockedResponseInterface->shouldReceive('getBody')
            ->once()
            ->andReturn(json_encode($res));

        $this->httpClient->shouldReceive('get')
            ->once()
            ->andReturn($this->mockedResponseInterface);

        $this->amplifyPay->getPaymentData('ssssss');

    }

}
