<?php

namespace Gbowo\Adapter\Paystack\Plugin;

use Gbowo\Plugin\AbstractPlugin;
use function GuzzleHttp\json_decode;

/**
 * Fetch all users that have interacted with the paystack api via your secret key.
 * This plugin is not added to the core. You'd have to add this your self.
 * @author Lanre Adelowo <me@adelowolanre.com>
 * Class GetAllCustomers
 * @package Gbowo\Adapter\Paystack\Plugin
 */
class GetAllCustomers extends AbstractPlugin
{

    /**
     * @var string
     */
    const CUSTOMERS_LINK = "customers";

    /**
     * The base url for the api
     * @var string
     */
    protected $baseUrl;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }


    public function getPluginAccessor() : string
    {
        return "getAllCustomers";
    }

    public function handle()
    {
        $response = json_decode(
            $this->adapter->getHttpClient()
                ->get(self::CUSTOMERS_LINK)
                ->getBody(),
            true
        );

        return $response['data'];
    }
}
