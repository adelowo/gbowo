<?php

namespace Gbowo\Adapter\Amplifypay;

use GuzzleHttp\Client;
use function Gbowo\env;
use Gbowo\Traits\Pluggable;
use function GuzzleHttp\json_decode;
use Psr\Http\Message\ResponseInterface;
use Gbowo\Contract\Adapter\AdapterInterface;
use Gbowo\Adapter\Amplifypay\Traits\KeyVerifier;
use Gbowo\Exception\InvalidHttpResponseException;
use Gbowo\Adapter\Amplifypay\Plugin\GetPaymentData;
use Gbowo\Adapter\Amplifypay\Plugin\ChargeWithToken;
use Gbowo\Adapter\Amplifypay\Plugin\UnsubscribeCustomer;

/**
 * @method chargeWithToken(array $data)
 * @method unsubcribeCustomerFromPlan(array $data)
 * @method getPaymentData(string $transactionReference)
 * @author Lanre Adelowo <yo@lanre.wtf>
 * Class AmplifypayAdapter
 * @package Gbowo\Adapter\Amplifypay
 */
class AmplifypayAdapter implements AdapterInterface
{

    use Pluggable, KeyVerifier;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * Keys to initiate a successful request
     * @var array
     */
    protected $apiKeys;

    /**
     * @var string
     */
    const BASE_URL = 'https://api.amplifypay.com/merchant';

    /**
     * Only pass a client constructor in if it (client object) has been properly bootstrapped
     * AmplifypayAdapter constructor.
     * @param \GuzzleHttp\Client|null $client
     */
    public function __construct(Client $client = null)
    {
        $this->httpClient = $client ?? $this->setHttpClient();

        $this->apiKeys = [
            "merchantId" => env("AMPLIFYPAY_MERCHANT_ID"),
            "apiKey" => env("AMPLIFYPAY_API_KEY")
        ];

        $this->registerPlugins();
    }

    protected function registerPlugins()
    {
        $this->addPlugin(new GetPaymentData(self::BASE_URL, $this->apiKeys))
            ->addPlugin(new ChargeWithToken(self::BASE_URL, $this->apiKeys))
            ->addPlugin(new UnsubscribeCustomer(self::BASE_URL, $this->apiKeys));
    }

    public function charge(array $data)
    {

        $response = $this->authorizeTransaction('/transact', array_merge($this->apiKeys, $data));

        $response = json_decode($response->getBody(), true);

        $this->verifyKeys($response['ApiKey'], $this->apiKeys['apiKey']);

        return $response['PaymentUrl'];
    }

    protected function authorizeTransaction(string $relative, array $data = null)
    {
        /**
         * @var ResponseInterface $response
         */
        $response = $this->httpClient->post(
            self::BASE_URL . $relative, [
            'body' => json_encode($data)
            ]
        );

        if ($response->getStatusCode() === 200) {
            return $response;
        }

        throw InvalidHttpResponseException::createFromResponse($response);
    }

    /**
     * @return \GuzzleHttp\Client
     */
    public function getHttpClient(): Client
    {
        return $this->httpClient;
    }

    /**
     * @codeCoverageIgnore
     * @return \GuzzleHttp\Client
     */
    protected function setHttpClient() : Client
    {
        return new Client(
            [
            'base_uri' => self::BASE_URL,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Cache-Control' => 'no-cache'
            ]
            ]
        );
    }
}
