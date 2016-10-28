<?php

namespace Gbowo\Plugin;

/**
 * Abstract plugin to allow a consistent api <method> `getPaymentData` for all adapters that provide this feature
 * @author Lanre Adelowo <me@adelowolanre.com>
 * Class AbstractGetPaymentData
 * @package Gbowo\Plugin
 */
abstract class AbstractGetPaymentData extends AbstractPlugin
{

    final public function getPluginAccessor() : string
    {
        return 'getPaymentData';
    }
}
