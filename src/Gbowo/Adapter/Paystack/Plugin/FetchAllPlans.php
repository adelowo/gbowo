<?php


namespace Gbowo\Adapter\Paystack\Plugin;

use function GuzzleHttp\json_decode;
use Gbowo\Plugin\AbstractFetchAllPlans;
use Psr\Http\Message\ResponseInterface;
use Gbowo\Exception\InvalidHttpResponseException;

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
     * @param array ...$args
     * @return mixed
     * @throws \Gbowo\Exception\InvalidHttpResponseException if the HTTP response status code is not 200
     */
    public function handle(...$args)
    {

        $params = "";

        if (count($args) !== 0) {

            $queryParams = $args[0];

            foreach ($queryParams as $key => $value) {
                if (reset($queryParams) == $value) {
                    $params .= "?{$key}={$value}";
                } else {
                    $params .= "&{$key}={$value}";
                }
            }
        }

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
