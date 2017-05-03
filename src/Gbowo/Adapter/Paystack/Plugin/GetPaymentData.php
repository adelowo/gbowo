<?php

namespace Gbowo\Adapter\Paystack\Plugin;

use Gbowo\Adapter\Paystack\Traits\VerifyHttpStatusResponseCode;
use function strcmp;
use function GuzzleHttp\json_decode;
use Gbowo\Plugin\AbstractGetPaymentData;
use Gbowo\Exception\TransactionVerficationFailedException;

class GetPaymentData extends AbstractGetPaymentData
{
    use VerifyHttpStatusResponseCode;

    const VERIFIED_TRANSACTION = 'Verification successful';

    const INVALID_TRANSACTION = "Invalid transaction reference";

    const TRANSACTION_VERIFICATION = '/transaction/verify/';

    protected $baseUrl;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @param string $reference
     * @return mixed
     * @throws \Gbowo\Adapter\Paystack\Exception\TransactionVerficationFailedException
     */
    public function handle(string $reference) : array
    {
        $link = $this->baseUrl . self::TRANSACTION_VERIFICATION . $reference;

        $response = $this->verifyTransaction($link);

        $this->verifyResponse($response);

        $result = json_decode($response->getBody(), true);

        $validated = false;

        if (strcmp($result['message'], self::VERIFIED_TRANSACTION) === 0) {
            $validated = true;
        }

        if (false === $validated) {
            throw TransactionVerficationFailedException::createFromResponse($response);
        }

        return $result["data"];
    }

    /**
     * @param string $link
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function verifyTransaction(string $link)
    {
        return $this->adapter->getHttpClient()
            ->get($link);
    }
}
