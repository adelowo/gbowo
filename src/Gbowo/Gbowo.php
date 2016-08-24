<?php

namespace Gbowo;

use Gbowo\Contract\Adapter\Adapter;

/**
 * @author Lanre Adelowo <me@adelowolanre.com>
 * Class Gbowo
 * @package Gbowo
 */
class Gbowo
{

    /**
     * @var \Gbowo\Contract\Adapter\Adapter
     */
    protected $paymentAdapter;

    public function __construct(Adapter $adapter)
    {
        $this->paymentAdapter = $adapter;
    }

    /**
     * @return \Gbowo\Contract\Adapter\Adapter
     */
    public function getPaymentAdapter(): Adapter
    {
        return $this->paymentAdapter;
    }


    /**
     * @param array|null $data
     * @return mixed
     */
    public function charge(array $data = null)
    {
        return $this->paymentAdapter->charge($data);
    }

    /**
     * We'd assume you are trying to access a plugin
     * @param string $method
     * @param array  $arg
     * @return mixed
     */
    public function __call(string $method, array $arg)
    {
        return $this->paymentAdapter->{$method}($arg[0]);
    }
}
