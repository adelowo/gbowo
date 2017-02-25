<?php

namespace Gbowo\Adapter\Paystack\Plugin;

use Gbowo\Plugin\AbstractPlugin;
use function GuzzleHttp\json_decode;

class GetAllCustomers extends AbstractPlugin
{
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
        $response = json_decode(
            $this->adapter->getHttpClient()
                ->get($this->baseUrl.self::CUSTOMERS_LINK)
                ->getBody(),
            true
        );

        return $response['data'];
    }
}
