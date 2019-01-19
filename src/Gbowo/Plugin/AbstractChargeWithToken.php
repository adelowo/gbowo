<?php

namespace Gbowo\Plugin;

/**
 * Abstract plugin for adapters that allows "re-charging" a customer without them providing their card details all the
 * time
 * @author Lanre Adelowo <yo@lanre.wtf>
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
