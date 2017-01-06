<?php

namespace Gbowo\Adapter\Paystack\Plugin;

use Gbowo\Plugin\AbstractPlugin;
use function GuzzleHttp\json_decode;

class GetCustomer extends AbstractPlugin
{

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

    public function handle(...$args)
    {

        $link = $this->baseUrl . str_replace(":id", $args[0], self::CUSTOMER_LINK);

        $result = json_decode($this->retrieveCustomerDetails($link), true);

        return $result;
    }

    protected function retrieveCustomerDetails(string $link)
    {
        return $this->adapter->getHttpClient()
            ->get($link)
            ->getBody();
    }
}
