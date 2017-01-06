<?php

namespace Gbowo\Adapter\Paystack\Plugin;

use function strcmp;
use function GuzzleHttp\json_decode;
use Gbowo\Plugin\AbstractGetPaymentData;
use Gbowo\Adapter\Paystack\Exception\TransactionVerficationFailedException;

class GetPaymentData extends AbstractGetPaymentData
{

    const VERIFIED_TRANSACTION = 'Verification successful';

    const INVALID_TRANSACTION = "Invalid transaction reference";

    const TRANSACTION_VERIFICATION = '/transaction/verify/';

    protected $baseUrl;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @param array ...$args
     * @return mixed
     * @throws \Gbowo\Adapter\Paystack\Exception\TransactionVerficationFailedException
     */
    public function handle(...$args)
    {

        $link = $this->baseUrl . self::TRANSACTION_VERIFICATION . $args[0];

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
