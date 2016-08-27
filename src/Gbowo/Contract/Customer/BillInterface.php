<?php

namespace Gbowo\Contract\Customer;

/**
 * This is an interface for adapters with plugins that allows recurrent payment without having to re-type their card
 * details
 * @author Lanre Adelowo <me@adelowolanre.com>
 * Interface Bill
 * @package Gbowo\Contract\Customer
 */
interface BillInterface
{

    /**
     * @param string|array $data
     * @return mixed
     */
    public function chargeByToken($data);
}
