<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Events\UserDataChanged;
use Illuminate\Support\Facades\Date;

class User_data extends Model
{
    /**
     * The attributes that are mass assignable.
     *          
     * @var array
     */
    protected $fillable = [
        'user_id', 'name', 'pic_url', 'area_code', "flag", 'whatsapp_number', 'link_facebook', 'link_instagram', 'link_twitter', 'description'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $primaryKey = "user_id";

    protected static $cache_duration = 86400; //un dia



    public static function get_data($user_id)
    {
        //sacamos lo que hay en caché
        //Cache::delete('user_data_' . $user_id);
        $in_cache = Cache::get('user_data_' . $user_id);

        if ($in_cache === null) {
            //sacamos lo que hay en la db 
            $in_db = self::where("user_id", $user_id)->first();

            if ($in_db !== null) {

                $in_db = $in_db->toArray();
                //guardamos en la caché
                $expiresAt = Date::now()->addMinutes(env('CACHE_LIFE_TIME_USER_DATA', 1));
                Cache::put('user_data_' . $user_id, json_encode($in_db), $expiresAt);

                return ["data" => $in_db, "from_cache" => false];
            }
            return ["data" => [], "from_cache" => false];
        }
        return ["data" => (array) json_decode($in_cache), "from_cache" => true];
    }



    public static function push_data($user_id, $data)
    {
        $user_data = self::get_data($user_id)["data"];

        //actualizamos la db
        $data["user_id"] = $user_id;
        $user_data = self::where('user_id', $user_id)->first();
        $new_user_data = $data;

        if ($user_data !== null) {
            $new_user_data = array_merge($user_data->toArray(), $data);
            $user_data->update($new_user_data);

        } else {
            $new_user_data["user_id"] = $user_id;
            self::create($new_user_data);
        }
       
        event(new UserDataChanged($new_user_data));

        return $new_user_data;
    }
}
