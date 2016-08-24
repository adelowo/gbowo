<?php

namespace Gbowo\Plugin;

use Gbowo\Contract\Plugin\Plugin;
use Gbowo\Contract\Adapter\Adapter;

/**
 * @author Lanre Adelowo <me@adelowolanre.com>
 * Class AbstractPlugin
 * @package Gbowo\Plugin
 */
abstract class AbstractPlugin implements Plugin
{

    /**
     * @var Adapter
     */
    protected $adapter;

    /**
     * @param \Gbowo\Contract\Adapter\Adapter $adapter
     * @return $this
     */
    public function setAdapter(Adapter $adapter)
    {
        $this->adapter = $adapter;

        return $this;
    }
}
