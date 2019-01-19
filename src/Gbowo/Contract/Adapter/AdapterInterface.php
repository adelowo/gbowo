<?php

namespace Gbowo\Contract\Adapter;

/**
 * @author Lanre Adelowo <yo@lanre.wtf>
 * Interface Adapter
 * @package Gbowo\Contract\Adapter
 */
interface AdapterInterface extends AdapterClientInterface
{

    /**
     * @param array $data Parameters to be handed over to the gateway such as amount,
     *                          blah-blah or gateway custom param
     * @return mixed
     */
    public function charge(array $data);
}
