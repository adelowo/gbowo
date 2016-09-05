<?php

namespace Gbowo\Tests\Adapter\Paystack\Plugin;


use DateTime;
use Gbowo\Tests\Mockable;
use Gbowo\Adapter\Paystack\PaystackAdapter;
use Gbowo\Adapter\Paystack\Plugin\GetPaymentData;
use Gbowo\Adapter\Paystack\Exception\TransactionVerficationFailedException;

class GetPaymentDataTest extends \PHPUnit_Framework_TestCase
{
    use Mockable;

    public function testGetPaymentPluginIsCalled()
    {

        $mockedInterface = $this->getMockedResponseInterface();

        $data = [
            'data' =>
                [
                    'id' => 5,
                    'authorization_code' => 'sss',
                    'transaction_date' => (new DateTime())->format('m-y-j')
                ],
            'message' => GetPaymentData::VERIFIED_TRANSACTION
        ];

        $mockedInterface->shouldReceive('getBody')
            ->once()
            ->andReturn(json_encode($data));

        $httpClient = $this->getMockedGuzzle();

        $httpClient->shouldReceive('get')
            ->once()
            ->andReturn($mockedInterface);

        $paystack = new PaystackAdapter($httpClient);

        $returnedData = $paystack->getPaymentData('token');

        $this->assertEquals($data, $returnedData);
    }

    /**
     * @expectedException \Gbowo\Adapter\Paystack\Exception\TransactionVerficationFailedException
     */
    public function testGetPaymentIsCalledButRaisesAnException()
    {
        $mockedInterface = $this->getMockedResponseInterface();

        $data = [
            'data' =>
                [
                    'id' => 5,
                    'authorization_code' => 'sss',
                    'transaction_date' => (new DateTime())->format('m-y-j')
                ],
            'message' => GetPaymentData::INVALID_TRANSACTION
        ];

        $mockedInterface->shouldReceive('getBody')
            ->once()
            ->andReturn(json_encode($data));

        $httpClient = $this->getMockedGuzzle();

        $httpClient->shouldReceive('get')
            ->once()
            ->andReturn($mockedInterface);

        $paystack = new PaystackAdapter($httpClient);

        $paystack->getPaymentData('token');

    }

}
