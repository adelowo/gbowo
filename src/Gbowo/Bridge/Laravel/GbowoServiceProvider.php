<?php

namespace Gbowo\Bridge\Laravel;

use Illuminate\Support\ServiceProvider;
use Gbowo\Adapter\Paystack\PaystackAdapter;
use Gbowo\Adapter\Amplifypay\AmplifypayAdapter;

/**
 * @codeCoverageIgnore
 * @author Lanre Adelowo <me@adelowolanre.com>
 * @package Gbowo\Bridge\Laravel
 */
class GbowoServiceProvider extends ServiceProvider
{

    protected $defer = true;

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([__DIR__ . '/../config/gbowo.php' => config_path('gbowo.php')]);

        $this->mergeConfigFrom(__DIR__ . '/../config/gbowo.php', 'gbowo');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerManager();
        $this->registerAdapters();
    }

    /**
     * Register the Manager class.
     *
     * @return void
     */
    protected function registerManager()
    {
        $this->app->singleton('gbowo', function ($app) {
            return new GbowoManager($app);
        });
    }

    /**
     * Register the default adapters that comes with the Gbowo Library.
     *
     * @return void
     *
     */
    protected function registerAdapters()
    {
        $this->app->bind("gbowo.paystack", function () {
            return new PaystackAdapter();
        });

        $this->app->bind("gbowo.amplifypay", function () {
            return new AmplifypayAdapter();
        });

    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return ["gbowo", "gbowo.paystack", "gbowo.amplifypay"];
    }
}
