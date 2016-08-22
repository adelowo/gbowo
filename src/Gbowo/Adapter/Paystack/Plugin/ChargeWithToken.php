<?php

namespace Gbowo\Adapter\Paystack\Plugin;


use GuzzleHttp\Client;
use Gbowo\Contract\Customer\Bill;
use Gbowo\Plugin\AbstractChargeWithToken;

/**
 * Charge a customer with the token returned from the first transaction initiated with the Paystack
 * @author Lanre Adelowo <me@adelowolanre.com>
 * Class ChargeWithToken
 * @package Gbowo\Adapter\Paystack\Plugin
 */
class ChargeWithToken extends AbstractChargeWithToken implements Bill
{

    /**
     * @var string
     */
    protected $baseUrl;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function handle(string $token)
    {
        return $this->chargeByToken($token);
    }

    public function chargeByToken($token)
    {

    }

}
