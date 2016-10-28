<?php


namespace Gbowo\Plugin;

/**
 * @author Lanre Adelowo <me@adelowolanre.com>
 * Class AbstractFetchAllPlans
 * @package Gbowo\Plugin
 */
abstract class AbstractFetchAllPlans extends AbstractPlugin
{

    final public function getPluginAccessor() : string
    {
        return 'fetchAllPlans';
    }
}
