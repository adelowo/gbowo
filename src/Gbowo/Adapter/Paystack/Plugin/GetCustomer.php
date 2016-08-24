<?php

namespace Gbowo\Adapter\Paystack\Plugin;

use Gbowo\Plugin\AbstractPlugin;
use function GuzzleHttp\json_decode;

/**
 * @author Lanre Adelowo <me@adelowolanre.com>
 * Class GetCustomer
 * @package Gbowo\Adapter\Paystack\Plugin
 */
class GetCustomer extends AbstractPlugin
{

    const CUSTOMER_LINK = '/customer/:id';

    /**
     * @var string
     */
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

    public function handle(int $id)
    {
        $link = $this->baseUrl . str_replace(":id", $id, self::CUSTOMER_LINK);

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
