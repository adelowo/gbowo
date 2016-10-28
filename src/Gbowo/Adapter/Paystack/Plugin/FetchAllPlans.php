<?php


namespace Gbowo\Adapter\Paystack\Plugin;

use function GuzzleHttp\json_decode;
use Gbowo\Plugin\AbstractFetchAllPlans;
use Psr\Http\Message\ResponseInterface;
use Gbowo\Exception\InvalidHttpResponseException;

/**
 * @author Lanre Adelowo <me@adelowolanre.com>
 * Class FetchAllPlans
 * @package Gbowo\Adapter\Paystack\Plugin
 */
class FetchAllPlans extends AbstractFetchAllPlans
{

    const FETCH_ALL_PLANS_RELATIVE_LINK = "/plan";

    /**
     * @var \Gbowo\Contract\Adapter\AdapterInterface
     */
    protected $adapter;

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * FetchAllPlans constructor.
     * @param string $baseUrl The base api link
     */
    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @param array $queryParams
     * @return mixed
     * @throws \Gbowo\Exception\InvalidHttpResponseException if the HTTP response status code is not 200
     */
    public function handle(array $queryParams = [])
    {
        $params = "?";

        foreach ($queryParams as $key => $value) {
            if (end($queryParams) !== $value) {
                $params .= "&{$key}={$value}";
            } else {
                $params .= "&{$key}={$value}";
            }
        }

        //the loop returns a string formatted as "?&page=1&clowns=yes".
        //Even though there is an initial `&` - after the query string .
        //It don't got no real meaning as it shouldn't affect the response.
        //Just to keep stuff neat, we stripping it out.
        $params = str_replace("?&", "?", $params);

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
