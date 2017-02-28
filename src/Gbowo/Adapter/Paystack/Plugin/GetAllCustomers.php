<?php

namespace Gbowo\Adapter\Paystack\Plugin;

use Gbowo\Adapter\Paystack\Traits\VerifyHttpStatusResponseCode;
use Gbowo\Plugin\AbstractPlugin;
use function GuzzleHttp\json_decode;

class GetAllCustomers extends AbstractPlugin
{

    use VerifyHttpStatusResponseCode;

    const CUSTOMERS_LINK = "/customer";

    protected $baseUrl;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }


    public function getPluginAccessor() : string
    {
        return "getAllCustomers";
    }

    public function handle() : array
    {
        $response = $this->adapter->getHttpClient()
            ->get($this->baseUrl . self::CUSTOMERS_LINK);

        $this->verifyResponse($response);

        return json_decode($response->getBody(), true)["data"];
    }
}
