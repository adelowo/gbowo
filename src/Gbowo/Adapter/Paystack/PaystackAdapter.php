<?php

namespace Gbowo\Adapter\Paystack;

use GuzzleHttp\Client;
use function Gbowo\env;
use Gbowo\Traits\Pluggable;
use Gbowo\Contract\Adapter\Adapter;
use function GuzzleHttp\json_decode;
use Psr\Http\Message\ResponseInterface;
use Gbowo\Adapter\Paystack\Traits\Payable;
use Gbowo\Adapter\Paystack\Plugin\GetPaymentData;
use Gbowo\Adapter\Paystack\Plugin\ChargeWithToken;

/**
 * @method findCustomer(int $customerId)
 * @method getPaymentData(string $transactionReference)
 * @method chargeWithToken(string $token)
 * @author Lanre Adelowo <me@adelowolanre.com>
 * Class PaystackAdapter
 * @package Gbowo\Adapter\Paystack
 */
class PaystackAdapter implements Adapter
{

    use Pluggable, Payable;

    /**
     * @var string
     */
    const API_LINK = 'https://api.paystack.co';

    /**
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * The Api link for paystack
     * @var string
     */
    protected $baseUrl;

    /**
     * PaystackAdapter constructor.
     * @param \GuzzleHttp\Client|null $client
     */
    public function __construct(Client $client = null)
    {
        $this->baseUrl = self::API_LINK;
        $this->httpClient = $client ?? $this->setHttpClient(env("PAYSTACK_SECRET_KEY"));
        $this->registerPlugins();
    }

    protected function registerPlugins()
    {
        $this->addPlugin(new GetPaymentData($this->baseUrl))
            ->addPlugin(new ChargeWithToken($this->baseUrl));
    }

    /**
     * @param array $data
     * @return string The authorization url to render the secure payment gateway.
     */
    public function charge(array $data)
    {

        $response = $this->decodeResponse(
            $this->authorizeTransaction("/transaction/initialize", $data),
            true
        );

        return $response['data']['authorization_url'];
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param bool                                $associative
     * @return mixed
     */
    protected function decodeResponse(ResponseInterface $response, $associative = false)
    {
        return json_decode($response->getBody(), $associative);
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
     * @param string $token
     * @return \GuzzleHttp\Client
     */
    protected function setHttpClient(string $token) : Client
    {
        return new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        ]);
    }
}
