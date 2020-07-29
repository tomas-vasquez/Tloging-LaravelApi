<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;

class AcademyCategories extends Model
{
    /**
     * The attributes that are mass assignable.
     *          
     * @var array
     */
    protected $fillable = [];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ["id"];



    public static function get_all()
    {
        //sacamos lo que hay en caché
        $in_cache = Cache::store('file')->get('academy-categories');

        if ($in_cache === null) {
            //sacamos lo que hay en la db 
            $in_db = self::all();
            //guardamos en caché
            $expiresAt = Date::now()->addMinutes(env('CACHE_LIFE_TIME_ACADEMY', 1));
            Cache::put('academy-categories', $in_db->toJson(), $expiresAt);
            return $in_db->toArray();
        }
        return json_decode($in_cache);
    }
}
