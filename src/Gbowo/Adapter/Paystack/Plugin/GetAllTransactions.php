<?php

namespace Gbowo\Adapter\Paystack\Plugin;

use Gbowo\Adapter\Paystack\Traits\VerifyHttpStatusResponseCode;
use Gbowo\Plugin\AbstractPlugin;
use function GuzzleHttp\json_decode;

class GetAllTransactions extends AbstractPlugin
{
    use VerifyHttpStatusResponseCode;

    const TRANSACTION_RELATIVE_LINK = "/transaction";

    protected $baseUrl ;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl ;
    }

    public function getPluginAccessor() : string
    {
        return "getAllTransactions";
    }

    public function handle() : array
    {

        $response = $this->adapter->getHttpClient()
            ->get($this->baseUrl . self::TRANSACTION_RELATIVE_LINK);

        $this->verifyResponse($response);

        return json_decode($response->getBody(), true)["data"];
    }
}
