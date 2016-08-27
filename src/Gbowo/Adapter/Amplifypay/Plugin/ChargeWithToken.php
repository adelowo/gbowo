<?php

namespace Gbowo\Adapter\Amplifypay\Plugin;

use Gbowo\Contract\Customer\BillInterface;
use function GuzzleHttp\json_decode;
use Gbowo\Plugin\AbstractChargeWithToken;
use Gbowo\Adapter\Amplifypay\Traits\KeyVerifier;
use Gbowo\Exception\InvalidHttpResponseException;
use Gbowo\Adapter\AmplifyPay\Exception\TransactionVerficationFailedException;

/**
 * A token in amplifypay refers to a pair of `transactionRef` and `authCode` gotten from a previously successful
 * payment.
 * @author Lanre Adelowo <me@adelowolanre.com>
 * Class ChargeWithToken
 * @package Gbowo\Adapter\Amplifypay\Plugin
 */
class ChargeWithToken extends AbstractChargeWithToken implements BillInterface
{

    use KeyVerifier;

    /**
     * @see https://amplifypay.com/developers Unsubscribe a customer from plan
     * @var string
     */
    const SUCCESSFUL_TRANSACTION = "Successfull Request";

    /**
     * @var string
     */
    const CHARGE_RETURNING_USER = "/returning/charge";

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var array
     */
    protected $apiKeys;

    public function __construct(string $baseUrl, array $apiKeys)
    {
        $this->baseUrl = $baseUrl;
        $this->apiKeys = $apiKeys;
    }

    /**
     * @param array $data
     * @return mixed
     * @throws \Gbowo\Adapter\AmplifyPay\Exception\TransactionVerficationFailedException
     * @throws \Gbowo\Exception\InvalidHttpResponseException
     */
    public function handle(array $data)
    {

        $response = $this->chargeByToken($data);

        if ($response->getStatusCode() !== 200) {
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

    public function chargeByToken($data)
    {
        $link = $this->baseUrl . self::CHARGE_RETURNING_USER;

        return $this->adapter->getHttpClient()
            ->post($link, array_merge($this->apiKeys, $data));
    }
}
