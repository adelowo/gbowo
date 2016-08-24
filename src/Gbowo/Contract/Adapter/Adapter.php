<?php

namespace Gbowo\Contract\Adapter;

/**
 * @author Lanre Adelowo <me@adelowolanre.com>
 * Interface Adapter
 * @package Gbowo\Contract\Adapter
 */
interface Adapter
{

    /**
     * @param array $data Parameters to be handed over to the gateway such as amount,
     *                          blah-blah or gateway custom param
     * @return mixed
     */
    public function charge(array $data);
}
