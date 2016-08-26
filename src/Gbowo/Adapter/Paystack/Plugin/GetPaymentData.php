<?php

namespace Gbowo\Adapter\Paystack\Plugin;

use function strcmp;
use function GuzzleHttp\json_decode;
use Gbowo\Plugin\AbstractGetPaymentData;
use Gbowo\Adapter\Paystack\Exception\TransactionVerficationFailedException;

/**
 * @author Lanre Adelowo <me@adelowolanre.com>
 * Class GetPaymentData
 * @package Adelowo\Gbowo\Adapter\Paystack\Plugin
 */
class GetPaymentData extends AbstractGetPaymentData
{

    const VERIFIED_TRANSACTION = 'Verification successful';

    const INVALID_TRANSACTION = "Invalid transaction reference";

    /**
     * @var string
     */
    const TRANSACTION_VERIFICATION = '/transaction/verify/';

    /**
     * @var string
     */
    protected $baseUrl;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @param string $reference
     * @return mixed
     * @throws \Gbowo\Adapter\Paystack\Exception\TransactionVerficationFailedException
     */
    public function handle(string $reference)
    {
        $link = $this->baseUrl . self::TRANSACTION_VERIFICATION . $reference;

        $result = json_decode($this->verifyTransaction($link), true);

        $validated = false;

        if (strcmp($result['message'], self::VERIFIED_TRANSACTION) === 0) {
            $validated = true;
        }

        if (false === $validated) {
            throw new TransactionVerficationFailedException(self::INVALID_TRANSACTION);
        }

        return $result;
    }

    /**
     * @param string $link
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function verifyTransaction(string $link)
    {
        return $this->adapter->getHttpClient()
            ->get($link)
            ->getBody();
    }
}
