<?php


namespace Gbowo\Plugin;

/**
 * @author Lanre Adelowo <yo@lanre.wtf>
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
