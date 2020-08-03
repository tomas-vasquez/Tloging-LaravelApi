<?php

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [

        \App\Events\UserDataChanged::class => [
            \App\Listeners\UpdateUserDataCacheListener::class,
            \App\Listeners\UpdateCommentsListener::class,
        ],

        \App\Events\PayReportEvent::class => [
            \App\Listeners\UpdatePayReportCacheListener::class,
        ],
    ];
}