<?php

namespace Webkul\TaVppTheme\Providers;

use Illuminate\Support\ServiceProvider;
use Webkul\TaVppTheme\Http\ViewComposers\ProductVoucherComposer;
use Illuminate\Support\Facades\View;

class TaVppThemeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/system.php', 'core'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/carriers.php', 'carriers'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/shop-routes.php');
        // Register view composer for product detail page
        // Queries database in ProductVoucherComposer (PHP)
        // and passes data to blade as $availableVouchers variable
        // ONLY runs when ta-vpp-theme::products.view is rendered
        View::composer(
            'shop::products.view',
            ProductVoucherComposer::class
        );

        // Register view composer for cart page to eager load product relationships
        View::composer(
            'shop::checkout.cart.index',
            \Webkul\TaVppTheme\Http\Composers\CartComposer::class
        );
        
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'ta-vpp-theme');

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'ta-vpp-theme');

        $this->publishes([
            __DIR__ . '/../Resources/views' => resource_path('themes/ta-vpp-theme/views'),
        ], 'ta-vpp-theme-views');

        $this->publishes([
            __DIR__ . '/../Resources/assets/images' => public_path('themes/ta-vpp-theme/images'),
        ], 'ta-vpp-theme-assets');

        
    }
}