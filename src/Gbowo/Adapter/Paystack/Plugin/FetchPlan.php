<?php


namespace Gbowo\Adapter\Paystack\Plugin;

use Gbowo\Adapter\Paystack\Traits\VerifyHttpStatusResponseCode;
use Gbowo\Plugin\AbstractFetchPlan;
use function GuzzleHttp\json_decode;
use Psr\Http\Message\ResponseInterface;

class FetchPlan extends AbstractFetchPlan
{

    use VerifyHttpStatusResponseCode;

    /**
     * @var string The relative link for fetching a certain plan
     */
    const FETCH_PLAN_LINK = '/plan/:identifier';

    protected $baseUrl;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @param string|int $identifier
     * @return mixed
     * @throws \Gbowo\Exception\InvalidHttpResponseException if ann http response of 200 isn't returned
     */
    public function handle($identifier)
    {
        $link = $this->baseUrl . str_replace(":identifier", $identifier, self::FETCH_PLAN_LINK);

        /**
         * @var ResponseInterface $response
         */
        $response = $this->adapter->getHttpClient()
            ->get($link);

        $this->verifyResponse($response);

        return json_decode($response->getBody(), true)['data'];
    }
}
