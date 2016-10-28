<?php

namespace Gbowo\Plugin;

use Gbowo\Contract\Plugin\PluginInterface;

/**
 * Abstract plugin for adapters that allows "re-charging" a customer without them providing their card details all the
 * time
 * @author Lanre Adelowo <me@adelowolanre.com>
 * Class AbstractChargeWithToken
 * @package Gbowo\Plugin
 */
abstract class AbstractChargeWithToken extends AbstractPlugin
{


    final public function getPluginAccessor() : string
    {
        return 'chargeWithToken';
    }
}
