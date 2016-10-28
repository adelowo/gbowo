<?php


namespace Gbowo\Adapter\Amplifypay\Plugin;

use Gbowo\Plugin\AbstractFetchPlan;
use function GuzzleHttp\json_decode;
use Psr\Http\Message\ResponseInterface;
use Gbowo\Exception\InvalidHttpResponseException;

/**
 * @author Lanre Adelowo <me@adelowolanre.com>
 * Class FetchPlan
 * @package Gbowo\Adapter\Amplifypay\Plugin
 */
class FetchPlan extends AbstractFetchPlan
{

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var array
     */
    protected $apiKeys;

    const FETCH_PLAN_RELATIVE_LINK = "/plan/:identifier";

    public function __construct(string $baseUrl, array $apiKeys)
    {
        $this->baseUrl = $baseUrl;
        $this->apiKeys= $apiKeys;
    }

    /**
     * @param $planIdentifier The id or string representation of the plan
     * @return mixed
     * @throws \Gbowo\Exception\InvalidHttpResponseException if the response code is not 200
     */
    public function handle($planIdentifier)
    {
        $link = $this->baseUrl."?PlanId={$planIdentifier}";

        /**
         * @var ResponseInterface $response
         */
        $response = $this->adapter->getHttpClient()
            ->get($link);

        if (200 !== $response->getStatusCode()) {
            throw new InvalidHttpResponseException(
                "Expected 200. Got {$response->getStatusCode()}"
            );
        }

        return json_decode($response->getBody(), true);
    }
}
