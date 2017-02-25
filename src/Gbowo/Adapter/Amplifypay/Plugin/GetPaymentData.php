<?php

namespace Gbowo\Adapter\Amplifypay\Plugin;

use Gbowo\Plugin\AbstractGetPaymentData;
use Gbowo\Adapter\Amplifypay\Traits\KeyVerifier;
use Gbowo\Exception\InvalidHttpResponseException;
use Gbowo\Adapter\AmplifyPay\Exception\TransactionVerficationFailedException;

class GetPaymentData extends AbstractGetPaymentData
{
    use KeyVerifier;

    const APPROVED_TRANSACTION_STATUS = "APPROVED";

    const UNAPPROVED_TRANSACTION_STATUS = "UNAPPROVED";

    const TRANSACTION_VERIFICATION = '/verify';

    protected $baseUrl;

    protected $apiKeys;

    public function __construct(string $baseUrl, array $apiKeys)
    {
        $this->baseUrl = $baseUrl;
        $this->apiKeys = $apiKeys;
    }

    /**
     * @param string $transactionRef
     * @return mixed
     * @throws \Gbowo\Adapter\AmplifyPay\Exception\TransactionVerficationFailedException if the transaction failed
     * @throws \Gbowo\Exception\InvalidHttpResponseException if the status code isn't 200
     */
    public function handle(string $transactionRef)
    {
        $link = $this->baseUrl .
            self::TRANSACTION_VERIFICATION .
            "?transactionRef={$transactionRef}&merchantId={$this->apiKeys['merchantId']}";

        $response = $this->verifyTransaction($link);

        $verificationResponse = json_decode($response->getBody(), true);

        if (200 !== $response->getStatusCode()) {
            throw new InvalidHttpResponseException(
                "Response status code must be 200. Got {$response->getStatusCode()} instead"
            );
        }

        $validated = false;

        if (strcmp($verificationResponse['OrderStatus'], self::APPROVED_TRANSACTION_STATUS) === 0) {
            $validated = true;
        }

        if (false === $validated) {
            throw new TransactionVerficationFailedException(self::UNAPPROVED_TRANSACTION_STATUS);
        }

        $this->verifyKeys($verificationResponse['ApiKey'], $this->apiKeys['apiKey']);

        //A `KeyMismatchException` would be thrown if they don't match.
        // Returning the response here signifies all went well.

        return $verificationResponse;
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
