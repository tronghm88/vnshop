<?php

namespace Webkul\VnRegionalShipping\Providers;

use Illuminate\Support\ServiceProvider;

class VnRegionalShippingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/provinces.php', 'provinces'
        );
        
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/system.php', 'core'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/carriers.php', 'carriers'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/provinces.php', 'provinces'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/routes.php');
        
        
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'vn-regional-shipping');

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'vn-regional-shipping');
    }
}