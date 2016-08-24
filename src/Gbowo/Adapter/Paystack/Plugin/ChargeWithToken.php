<?php

namespace Gbowo\Adapter\Paystack\Plugin;

use Gbowo\Contract\Customer\Bill;
use function GuzzleHttp\json_decode;
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
        return json_decode($this->chargeByToken($token), true);
    }

    public function chargeByToken($token)
    {

    }
}
