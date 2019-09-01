<?php

namespace Gbowo\Adapter\Paystack;

use GuzzleHttp\Client;
use function Gbowo\env;
use Gbowo\Traits\Pluggable;
use Gbowo\Contract\Adapter\AdapterInterface;
use function GuzzleHttp\json_decode;
use Psr\Http\Message\ResponseInterface;
use Gbowo\Adapter\Paystack\Traits\Payable;
use Gbowo\Adapter\Paystack\Plugin\GetPaymentData;
use Gbowo\Adapter\Paystack\Plugin\ChargeWithToken;
use Gbowo\Adapter\Paystack\Traits\VerifyHttpStatusResponseCode;

/**
 * @method findCustomer(int $customerId)
 * @method getPaymentData(string $transactionReference)
 * @method chargeWithToken(array $data)
 * @author Lanre Adelowo <yo@lanre.wtf>
 * Class PaystackAdapter
 * @package Gbowo\Adapter\Paystack
 */
class PaystackAdapter implements AdapterInterface
{
    use Pluggable, Payable, VerifyHttpStatusResponseCode;

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
        $response = $this->authorizeTransaction("/transaction/initialize", $data);

        $this->verifyResponse($response);

        return $this->decodeResponse($response)['data']['authorization_url'];
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return mixed
     */
    protected function decodeResponse(ResponseInterface $response)
    {
        return json_decode($response->getBody(), true);
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
        return new Client(
            [
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'User-Agent' => 'PHP/Gbowo'
            ]
            ]
        );
    }
}
