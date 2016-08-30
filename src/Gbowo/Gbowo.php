<?php

namespace Gbowo;

use Gbowo\Contract\Adapter\AdapterInterface;

/**
 * @author Lanre Adelowo <me@adelowolanre.com>
 * Class Gbowo
 * @package Gbowo
 */
class Gbowo
{

    /**
     * @var \Gbowo\Contract\Adapter\AdapterInterface
     */
    protected $paymentAdapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->paymentAdapter = $adapter;
    }

    /**
     * @return \Gbowo\Contract\Adapter\AdapterInterface
     */
    public function getPaymentAdapter(): AdapterInterface
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
        return call_user_func_array([$this->paymentAdapter, $method], $arg);
    }
}
