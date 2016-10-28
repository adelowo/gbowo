<?php


namespace Gbowo\Plugin;

/**
 * Abstract plugin to allow a consistent api amongst adapters that support "fetching a specific plan"
 * @author Lanre Adelowo <me@adelowolanre.com>
 * Class AbstractFetchPlan
 * @package Gbowo\Plugin
 */
abstract class AbstractFetchPlan extends AbstractPlugin
{

    final public function getPluginAccessor() :string
    {
        return 'fetchPlan';
    }
}
