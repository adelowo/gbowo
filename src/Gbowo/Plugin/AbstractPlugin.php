<?php

namespace Gbowo\Plugin;

use Gbowo\Contract\Plugin\PluginInterface;
use Gbowo\Contract\Adapter\AdapterInterface;

/**
 * @author Lanre Adelowo <me@adelowolanre.com>
 * Class AbstractPlugin
 * @package Gbowo\Plugin
 */
abstract class AbstractPlugin implements PluginInterface
{

    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @param \Gbowo\Contract\Adapter\AdapterInterface $adapter
     * @return $this
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;

        return $this;
    }
}
