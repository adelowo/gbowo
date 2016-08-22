<?php

namespace Gbowo\Contract\Plugin;

use Gbowo\Contract\Adapter\Adapter;

/**
 * @author Lanre Adelowo <me@adelowolanre.com>
 * Interface Plugin
 * @package Gbowo\Contract\Plugin
 */
interface Plugin
{

    public function getPluginAccessor() : string;


    public function setAdapter(Adapter $adapter);

}