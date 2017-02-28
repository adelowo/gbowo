<?php

namespace Gbowo\Adapter\Paystack\Plugin;

use Gbowo\Adapter\Paystack\Traits\VerifyHttpStatusResponseCode;
use Gbowo\Plugin\AbstractPlugin;
use function GuzzleHttp\json_decode;

class GetCustomer extends AbstractPlugin
{
    use VerifyHttpStatusResponseCode;

    const CUSTOMER_LINK = '/customer/:id';

    protected $baseUrl;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function getPluginAccessor() : string
    {
        return "getCustomer";
    }

    public function handle(string $customerId) : array
    {
        $link = $this->baseUrl . str_replace(":id", $customerId, self::CUSTOMER_LINK);

        $response = $this->retrieveCustomerDetails($link);

        $this->verifyResponse($response);

        return json_decode($response->getBody(), true)["data"];

    }

    protected function retrieveCustomerDetails(string $link)
    {
        return $this->adapter->getHttpClient()
            ->get($link);
    }
}
