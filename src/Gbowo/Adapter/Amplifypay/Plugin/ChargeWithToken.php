<?php

namespace Gbowo\Adapter\Amplifypay\Plugin;

use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;
use Gbowo\Plugin\AbstractChargeWithToken;
use Gbowo\Adapter\Amplifypay\Traits\KeyVerifier;
use Gbowo\Exception\InvalidHttpResponseException;
use Gbowo\Adapter\AmplifyPay\Exception\TransactionVerficationFailedException;
use Psr\Http\Message\ResponseInterface;

class ChargeWithToken extends AbstractChargeWithToken
{

    use KeyVerifier;

    const SUCCESSFUL_TRANSACTION = "Successfull Request";

    const CHARGE_RETURNING_USER = "/returning/charge";

    protected $baseUrl;

    protected $apiKeys;

    public function __construct(string $baseUrl, array $apiKeys)
    {
        $this->baseUrl = $baseUrl;
        $this->apiKeys = $apiKeys;
    }

    /**
     * @param array ...$args
     * @return mixed
     * @throws \Gbowo\Adapter\AmplifyPay\Exception\TransactionVerficationFailedException
     * @throws \Gbowo\Exception\InvalidHttpResponseException
     */
    public function handle(...$args)
    {

        $response = $this->chargeByToken($args[0]);

        if (200 !== $response->getStatusCode()) {
            throw new InvalidHttpResponseException(
                "Expected 200 . Got {$response->getStatusCode()}"
            );
        }

        $response = json_decode($response->getBody(), true);

        $validated = false;

        if (strcmp($response['StatusDesc'], self::SUCCESSFUL_TRANSACTION) === 0) {
            $validated = true;
        }

        if (false === $validated) {
            throw new TransactionVerficationFailedException(
                "Could not verify that the customer was unsubscribed"
            );
        }

        $this->verifyKeys($response['apiKey'], $this->apiKeys['apiKey']);

        return $response;
    }


    /**
     * @param array $data
     * @return ResponseInterface
     */
    public function chargeByToken($data)
    {
        $link = $this->baseUrl . self::CHARGE_RETURNING_USER;

        return $this->adapter->getHttpClient()
            ->post($link, [
                'body' => json_encode(array_merge($this->apiKeys, $data))
            ]);
    }
}
