<?php

namespace Gbowo\Traits;

use Gbowo\Contract\Plugin\PluginInterface;
use Gbowo\Contract\Adapter\AdapterInterface;
use Gbowo\Exception\PluginNotFoundException;

trait Pluggable
{

    /**
     * @var PluginInterface[]
     */
    protected $plugins = [];

    /**
     * Add a plugin
     * @param \Gbowo\Contract\Plugin\PluginInterface $plugin
     * @return $this
     */
    public function addPlugin(PluginInterface $plugin)
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
    public function __call(
        string $pluginAccessor,
        array $argument
    ) {
        return $this->callPlugin($pluginAccessor, $argument, $this);
    }

    /**
     * @param string                                   $accessor The plugin accessor
     * @param array                                    $argument Args to pass to the plugin's `handle` method
     * @param \Gbowo\Contract\Adapter\AdapterInterface $adapter The adapter in use.
     * @return mixed
     */
    public function callPlugin(
        string $accessor,
        array $argument,
        AdapterInterface $adapter
    ) {
        $plugin = $this->getPlugin($accessor);
        $plugin->setAdapter($adapter);

        return $plugin->handle(...$argument);
    }

    /**
     * @param string $accessor
     * @return \Gbowo\Contract\Plugin\PluginInterface
     * @throws \Gbowo\Exception\PluginNotFoundException If the plugin cannot be found
     * @throws LogicException if the plugin does not have an method called `handle`
     */
    public function getPlugin(string $accessor)
    {
        if (!isset($this->plugins[$accessor])) {
            throw new PluginNotFoundException(
                "Plugin with accessor, {$accessor} not found"
            );
        }

        return $this->plugins[$accessor];
    }
}
