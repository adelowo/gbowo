<?php

namespace Gbowo\Bridge\Laravel;

use Closure;
use InvalidArgumentException;
use Illuminate\Contracts\Foundation\Application;

/**
 * @codeCoverageIgnore
 * @author Lanre Adelowo <me@adelowolanre.com>
 * Class GbowoManager
 * @package Gbowo\Bridge\Laravel
 */
class GbowoManager
{

    /**
     * Custom adapters
     * @var array
     */
    protected $customAdapters = [];


    /**
     * Resolved adapters
     * @var \Gbowo\Contract\Adapter\AdapterInterface[]
     */
    protected $adapters = [];

    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param string $name
     * @return \Gbowo\Contract\Adapter\AdapterInterface
     */
    public function adapter(string $name = null)
    {
        return $this->make($name);
    }

    protected function make(string $name = null)
    {
        $adapter = $name ?? $this->getDefaultDriver();

        return $this->adapters[$adapter] = $this->getAdapter($adapter);
    }

    public function getDefaultDriver()
    {
        return $this->app['config']['gbowo.default'];
    }

    protected function getAdapter(string $name)
    {
        return isset($this->adapters[$name]) ? $this->adapters[$name] : $this->resolveAdapter($name);
    }

    protected function resolveAdapter(string $name)
    {

        if (isset($this->customAdapters[$name])) {
            return $this->customAdapters[$name]();
        }

        $adapters = $this->app['config']['gbowo.adapters'];

        if (!array_key_exists($name, $adapters)) {
            throw new InvalidArgumentException(
                "The specified adapter, {$name} is not supported"
            );
        }

        $adapter = $adapters[$name]['driver'];
        $method = "create" . ucfirst($adapter) . "Adapter";

        return $this->{$method}();
    }

    /**
     * @return \Gbowo\Contract\Adapter\AdapterInterface
     */
    public function createPaystackAdapter()
    {
        return $this->app->make("gbowo.paystack");
    }

    /**
     * @return \Gbowo\Contract\Adapter\AdapterInterface
     */
    public function createAmplifyPayAdapter()
    {
        return $this->app->make("gbowo.amplifypay");
    }

    /**
     * Register a custom adapter.
     * @param string   $adapterName
     * @param \Closure $callback
     * @return void
     */
    public function extend(string $adapterName, Closure $callback)
    {
        $this->customAdapters[$adapterName] = $callback;
    }
}
