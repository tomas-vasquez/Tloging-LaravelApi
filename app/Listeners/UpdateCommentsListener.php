<?php

namespace App\Listeners;

use App\Events\UserDataChanged;
use App\ChromePhp;
use App\Comments;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;

class UpdateCommentsListener
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
        $data = ["comment_updated_at" => time()];
        $result = Comments::where("comment_author_id", $user_data["user_id"])
            ->orWhere("comment_reply_id", $user_data["user_id"])
            ->update($data);
        if (env('APP_DEBUG', false)) {
            ChromePhp::log("comentarios actualizados", $result);
        }
    }
}
