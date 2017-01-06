<?php


namespace Gbowo\Adapter\Paystack\Plugin;

use Gbowo\Plugin\AbstractFetchPlan;
use function GuzzleHttp\json_decode;
use Psr\Http\Message\ResponseInterface;
use Gbowo\Exception\InvalidHttpResponseException;

class FetchPlan extends AbstractFetchPlan
{

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
     * @param array ...$args
     * @return mixed
     * @throws \Gbowo\Exception\InvalidHttpResponseException if ann http response of 200 isn't returned
     */
    public function handle(...$args)
    {

        $link = $this->baseUrl . str_replace(":identifier", $args[0], self::FETCH_PLAN_LINK);

        /**
         * @var ResponseInterface $response
         */
        $response = $this->adapter->getHttpClient()
            ->get($link);

        if (200 !== $response->getStatusCode()) {
            throw new InvalidHttpResponseException(
                "Expected 200. Got {$response->getStatusCode()} instead"
            );
        }

        return json_decode($response->getBody(), true)['data'];
    }
}
