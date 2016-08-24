<?php

namespace Gbowo\Traits;

use LogicException;
use Gbowo\Contract\Plugin\Plugin;
use Gbowo\Contract\Adapter\Adapter;
use Gbowo\Exception\PluginNotFoundException;

/**
 * Thank you Flysystem for this.
 * @author Lanre Adelowo <me@adelowolanre.com>
 * Class Pluggable
 * @package Gbowo\Traits
 */
trait Pluggable
{

    /**
     * @var Plugin[]
     */
    protected $plugins = [];

    /**
     * Add a plugin
     * @param \Gbowo\Contract\Plugin\Plugin $plugin
     * @return $this
     */
    public function addPlugin(Plugin $plugin)
    {
        $this->plugins[$plugin->getPluginAccessor()] = $plugin;

        return $this;
    }

    /**
     * Magic method to allow for a plugin call
     * @param string $pluginAccessor
     * @param array  $argument
     * @return mixed
     */
    public function __call(string $pluginAccessor, array $argument)
    {
        return $this->callPlugin($pluginAccessor, $argument, $this);
    }

    /**
     * @param string                          $accessor The plugin accessor
     * @param array                           $argument Args to pass to the plugin's `handle` method
     * @param \Gbowo\Contract\Adapter\Adapter $adapter The adapter in use.
     * @return mixed
     */
    public function callPlugin(string $accessor, array $argument, Adapter $adapter)
    {
        $plugin = $this->getPlugin($accessor);
        $plugin->setAdapter($adapter);

        return call_user_func_array([$plugin, 'handle'], $argument);
    }

    /**
     * @param string $accessor
     * @return \Gbowo\Contract\Plugin\Plugin
     * @throws \Gbowo\Exception\PluginNotFoundException If the plugin cannot be found
     * @throws LogicException if the plugin does not have an method called `handle`
     */
    public function getPlugin(string $accessor)
    {

        if (!isset($this->plugins[$accessor])) {
            throw new PluginNotFoundException("Plugin with accessor, {$accessor} not found");
        }

        if (!method_exists($this->plugins[$accessor], 'handle')) {
            throw new LogicException(
                get_class($this->plugins[$accessor]) . ' is not a callable plugin as it does not have an handle method'
            );
        }

        return $this->plugins[$accessor];
    }
}
