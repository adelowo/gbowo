<?php

namespace Gbowo\Adapter\Paystack\Traits;

use function GuzzleHttp\json_encode;
use Psr\Http\Message\ResponseInterface;
use Gbowo\Exception\InvalidHttpResponseException;

/**
 * @author Lanre Adelowo <me@adelowolanre.com>
 * Class Payable
 * @package Gbowo\Adapter\Paystack\Traits
 */
trait Payable
{

    /**
     * @param string $relative
     * @param array  $data
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Gbowo\Exception\InvalidHttpResponseException if the status code is not 200
     */
    protected function authorizeTransaction(string $relative, array $data = null)
    {
        /**
         * @var ResponseInterface $data
         */
        $data = $this->httpClient->post($this->baseUrl . $relative, [
            'body' => json_encode($data)
        ]);

        if ($data->getStatusCode() === 200) {
            return $data;
        }

        throw new InvalidHttpResponseException(
            "Response Status should be 200, but got {$data->getStatusCode()}"
        );
    }

}