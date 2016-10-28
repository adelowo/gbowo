<?php

namespace Gbowo\Plugin;

use Gbowo\Contract\Plugin\PluginInterface;

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
