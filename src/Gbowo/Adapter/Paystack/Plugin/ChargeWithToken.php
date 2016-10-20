<?php

namespace Gbowo\Adapter\Paystack\Plugin;

use InvalidArgumentException;
use Gbowo\Contract\Customer\BillInterface;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;
use Gbowo\Plugin\AbstractChargeWithToken;
use Gbowo\Adapter\Paystack\Exception\TransactionVerficationFailedException;

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
    const TOKEN_CHARGE_RELATIVE_LINK = "/transaction/charge_authorization";

    const SUCCESS_MESSAGE = "Successful";
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
        $response = $this->chargeByToken($data);

        $res = json_decode($response->getBody(), true);

        if (strcmp($res['data']['gateway_response'], self::SUCCESS_MESSAGE) !== 0) {
            throw new TransactionVerficationFailedException(
                "The transaction was not successful"
            );
        }

        return $res['data'];
    }

    public function chargeByToken($data)
    {
        return $this->adapter->getHttpClient()
            ->post($this->baseUrl . self::TOKEN_CHARGE_RELATIVE_LINK, [
                'body' => json_encode($data)
            ]);
    }
}
