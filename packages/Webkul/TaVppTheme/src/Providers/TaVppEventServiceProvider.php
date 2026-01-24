<?php

namespace Webkul\TaVppTheme\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class TaVppEventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'customer.review.update.after' => [
            'Webkul\TaVppTheme\Listeners\Review@afterUpdate',
        ],

        'customer.review.delete.before' => [
            'Webkul\TaVppTheme\Listeners\Review@beforeDelete',
        ],
    ];
}
