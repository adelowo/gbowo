<?php

namespace Gbowo\Adapter\Amplifypay;

use GuzzleHttp\Client;
use function Gbowo\env;
use Gbowo\Traits\Pluggable;
use Gbowo\Contract\Adapter\Adapter;
use function GuzzleHttp\json_decode;
use Psr\Http\Message\ResponseInterface;
use Gbowo\Adapter\Amplifypay\Traits\KeyVerifier;
use Gbowo\Exception\InvalidHttpResponseException;
use Gbowo\Adapter\Amplifypay\Plugin\GetPaymentData;
use Gbowo\Adapter\Amplifypay\Plugin\ChargeWithToken;
use Gbowo\Adapter\Amplifypay\Plugin\UnsubscribeCustomer;

/**
 * @method chargeWithToken(array $data)
 * @method unsubcribeCustomerFromPlan(array $data)
 * @method getPaymentData(string $transactionReference)
 * @author Lanre Adelowo <me@adelowolanre.com>
 * Class AmplifypayAdapter
 * @package Gbowo\Adapter\Amplifypay
 */
class AmplifypayAdapter implements Adapter
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
    protected $baseUrl = 'https://api.amplifypay.com/merchant';

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

    /**
     * @codeCoverageIgnore
     * @return \GuzzleHttp\Client
     */
    protected function setHttpClient() : Client
    {
        return new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Cache-Control' => 'no-cache'
            ]
        ]);
    }

    protected function registerPlugins()
    {
        $this->addPlugin(new GetPaymentData($this->baseUrl, $this->apiKeys))
            ->addPlugin(new ChargeWithToken($this->baseUrl, $this->apiKeys))
            ->addPlugin(new UnsubscribeCustomer($this->baseUrl, $this->apiKeys));
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
         * @var ResponseInterface $data
         */
        $data = $this->httpClient->post($this->baseUrl . $relative, [
            'body' => json_encode($data)
        ]);

        if ($data->getStatusCode() === 200) {
            return $data;
        }

        throw new InvalidHttpResponseException(
            "Response Status should be 200, but got {$data->getStatusCode()}"
        );
    }

    /**
     * @return \GuzzleHttp\Client
     */
    public function getHttpClient(): Client
    {
        return $this->httpClient;
    }
}
