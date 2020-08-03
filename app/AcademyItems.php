<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;

class AcademyItems extends Model
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
    protected $hidden = [];



    public static function get_items($course_title)
    {
        
        
        $course_title = str_replace("_", " ", $course_title);

        //buscamos el id en tabla del curso
        $courses = AcademyCourses::get_all();

        $id = null;
        foreach ($courses as $key => $course) {
            $course = (array)$course;
            // dd($course);
            if ($course["course_short_link"] === $course_title) {
                $id = $course["id"];
            }
    Cache::delete('items-course-'.$id);    }

        //con el id buscamos sus items
        if ($id !== null) {
            //sacamos lo que hay en caché
            $in_cache = Cache::store('file')->get('items-course-'.$id);

            if ($in_cache === null) {
                //sacamos lo que hay en la db 
                $courses = self::where("item_course_id", $id)->get();

                // dd($courses->toArray());
                //guardamos en caché
                $expiresAt = Date::now()->addMinutes(env('CACHE_LIFE_TIME_ACADEMY', 1));
                Cache::put('items-course-'.$id, $courses->toJson(), $expiresAt);

                return $courses->toArray();
            }
            return json_decode($in_cache);
        } else {
        }
    }
}
