<?php

namespace App\Listeners;

use App\Events\UserDataChanged;
use App\ChromePhp;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;

class UpdateUserDataCacheListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\ExampleEvent  $event
     * @return void
     */
    public static function handle(UserDataChanged $event)
    {
        $user_data = $event->new_user_data;
        $user_id = $user_data["user_id"];

        if (Cache::get('user_data_' . $user_id) !== null) {
            $expiresAt = Date::now()->addMinutes(env('CACHE_LIFE_TIME_USER_DATA', 1));
            Cache::put('user_data_' . $user_id, json_encode($user_data), $expiresAt);

            if (env('APP_DEBUG', false)){
                ChromePhp::log("updateCache: user_data_" . $user_id . ":", $user_data);
            }
        } else {

            if (env('APP_DEBUG', false)){
                ChromePhp::log("updateCache: user_data_" . $user_id . ": null");
            }
        }
    }
}
