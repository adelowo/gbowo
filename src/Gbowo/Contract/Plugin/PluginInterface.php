<?php

namespace Gbowo\Contract\Plugin;

use Gbowo\Contract\Adapter\AdapterInterface;

/**
 * @author Lanre Adelowo <me@adelowolanre.com>
 * Interface Plugin
 * @package Gbowo\Contract\Plugin
 */
interface PluginInterface
{

    public function getPluginAccessor() : string;


    public function setAdapter(AdapterInterface $adapter);
}
