<?php


namespace Gbowo\Adapter\Amplifypay\Plugin;

use Gbowo\Plugin\AbstractFetchPlan;
use function GuzzleHttp\json_decode;
use Psr\Http\Message\ResponseInterface;
use Gbowo\Exception\InvalidHttpResponseException;

class FetchPlan extends AbstractFetchPlan
{
    protected $baseUrl;

    protected $apiKeys;

    const FETCH_PLAN_RELATIVE_LINK = "/plan";

    public function __construct(string $baseUrl, array $apiKeys)
    {
        $this->baseUrl = $baseUrl;
        $this->apiKeys= $apiKeys;
    }

    /**
     * @param string $planId
     * @return mixed
     * @throws \Gbowo\Exception\InvalidHttpResponseException if the response code is not 200
     */
    public function handle(string $planId)
    {
        $link = $this->baseUrl.self::FETCH_PLAN_RELATIVE_LINK."?PlanId={$planId}";

        /**
         * @var ResponseInterface $response
         */
        $response = $this->adapter->getHttpClient()
            ->get($link);

        if ($response->getStatusCode() !== 200) {
            throw InvalidHttpResponseException::createFromResponse($response);
        }

        return json_decode($response->getBody(), true);
    }
}
