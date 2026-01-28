<?php

namespace Webkul\Suggestion\Providers;

use Illuminate\Support\ServiceProvider;

class SuggestionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->register(EventServiceProvider::class);

        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'suggestion');

        $this->loadRoutesFrom(__DIR__.'/../Routes/shop-routes.php');

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'suggestion');

        $this->publishes([
            __DIR__.'/../Resources/views/components/layouts/header/mobile/index.blade.php' => __DIR__.'/../../../Shop/src/Resources/views/components/layouts/header/mobile/index.blade.php',
        ]);

        $this->publishes([
            __DIR__.'/../Resources/views/components/layouts/header/desktop/bottom.blade.php' => __DIR__.'/../../../Shop/src/Resources/views/components/layouts/header/desktop/bottom.blade.php',
        ]);

        $this->publishes([
            __DIR__.'/../Resources/views/search/index.blade.php' => __DIR__.'/../../../Shop/src/Resources/views/search/index.blade.php',
        ]);

        view()->composer(
            'ta-vpp-theme::components.search-bar',
            \Webkul\Suggestion\ViewComposers\SearchSuggestionComposer::class
        );
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();
    }

    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/system.php', 'core'
        );
        
    }
}
