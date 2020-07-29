<?php

namespace App\Events;


class UserDataChanged extends Event
{

    public $new_user_data;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($new_user_data)
    {
        $this->new_user_data = $new_user_data;
    }
}
