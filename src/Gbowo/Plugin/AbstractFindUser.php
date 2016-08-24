<?php

namespace Gbowo\Plugin;

use Gbowo\Contract\Plugin\Plugin;

/**
 * Abstract plugin to allow a consistent api <method> `find` for all adapters that provide this feature i.e finding a
 * customer from their dashboard.
 * @author Lanre Adelowo <me@adelowolanre.com>
 * Class AbstractFindUser
 * @package Gbowo\Plugin
 */
abstract class AbstractFindUser extends AbstractPlugin implements Plugin
{


    final public function getPluginAccessor() : string
    {
        return 'findCustomer';
    }
}
