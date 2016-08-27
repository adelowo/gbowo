<?php

namespace Gbowo\Adapter\Paystack\Plugin;

use Gbowo\Adapter\Paystack\Exception\TransactionVerficationFailedException;
use InvalidArgumentException;
use Gbowo\Contract\Customer\BillInterface;
use function GuzzleHttp\json_decode;
use Gbowo\Plugin\AbstractChargeWithToken;

/**
 * Charge a customer with the token returned from the first transaction initiated with the Paystack
 * @author Lanre Adelowo <me@adelowolanre.com>
 * Class ChargeWithToken
 * @package Gbowo\Adapter\Paystack\Plugin
 */
class ChargeWithToken extends AbstractChargeWithToken implements BillInterface
{

    /**
     * The relative link for charging users
     * @var string
     */
    const TOKEN_CHARGE_RELATIVE_LINK = "/charge_token";

    const SUCCESS_MESSAGE = "Charge successful";
    /**
     * @var string
     */
    protected $baseUrl;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function handle(array $data)
    {
        if (!array_key_exists("token", $data)) {
            throw new InvalidArgumentException(
                "A token must be specified"
            );
        }

        $response = $this->chargeByToken($data);

        $res = json_decode($response->getBody(), true);

        if (strcmp($res['message'], self::SUCCESS_MESSAGE) !== 0) {
            throw new TransactionVerficationFailedException(
                "The transaction was not successful"
            );
        }

        return $res['data'];
    }

    public function chargeByToken($token)
    {
        return $this->adapter->getHttpClient()
            ->post($this->baseUrl . self::TOKEN_CHARGE_RELATIVE_LINK);
    }
}
