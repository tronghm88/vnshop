<?php

namespace Webkul\Suggestion\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen('bagisto.shop.layout.head.before', function ($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('suggestion::style');
        });
    }
}
