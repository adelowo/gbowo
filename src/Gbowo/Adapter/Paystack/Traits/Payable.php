<?php

namespace Gbowo\Adapter\Paystack\Traits;

use function GuzzleHttp\json_encode;
use Psr\Http\Message\ResponseInterface;

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
     */
    protected function authorizeTransaction(string $relative, array $data = [])
    {
        return $this->httpClient->post($this->baseUrl . $relative, [
            'body' => json_encode($data)
        ]);
    }
}
