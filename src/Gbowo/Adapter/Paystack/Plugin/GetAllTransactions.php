<?php

namespace Gbowo\Adapter\Paystack\Plugin;

use Gbowo\Plugin\AbstractPlugin;
use function GuzzleHttp\json_decode;

/**
 *
 */
class GetAllTransactions extends AbstractPlugin
{

    /**
     * Append this to the base url
     * @var string
     */
    const TRANSACTION_RELATIVE_LINK = "/transaction";

    /**
     * Paystack api's base uri
     * @var string
     */
    protected $baseUrl ;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl ;
    }

    public function getPluginAccessor() : string
    {
        return "getAllTransactions";
    }

    public function handle()
    {

        $response = json_decode(
            $this->adapter->getHttpClient()
                ->get($this->baseUrl.self::TRANSACTION_RELATIVE_LINK)
                ->getBody(),
            true
        );

        return $response['data'];
    }
}
