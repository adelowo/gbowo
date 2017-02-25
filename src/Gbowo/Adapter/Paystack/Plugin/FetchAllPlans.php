<?php


namespace Gbowo\Adapter\Paystack\Plugin;

use Gbowo\Exception\InvalidHttpResponseException;
use Gbowo\Plugin\AbstractFetchAllPlans;
use function Gbowo\toQueryParams;
use Psr\Http\Message\ResponseInterface;
use function GuzzleHttp\json_decode;

class FetchAllPlans extends AbstractFetchAllPlans
{
    const FETCH_ALL_PLANS_RELATIVE_LINK = "/plan";

    /**
     * @var \Gbowo\Contract\Adapter\AdapterInterface
     */
    protected $adapter;

    protected $baseUrl;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @param array $args
     * @return mixed
     * @throws \Gbowo\Exception\InvalidHttpResponseException if the HTTP response status code is not 200
     */
    public function handle(array $args = [])
    {
        $params = toQueryParams($args);

        /**
         * @var ResponseInterface $response
         */
        $response = $this->adapter->getHttpClient()
            ->get($this->baseUrl . self::FETCH_ALL_PLANS_RELATIVE_LINK . $params);

        if (200 !== $response->getStatusCode()) {
            throw new InvalidHttpResponseException(
                "Expected 200. Got {$response->getStatusCode()} instead"
            );
        }

        return json_decode($response->getBody(), true)['data'];
    }
}
