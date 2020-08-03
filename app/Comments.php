<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    /**
     * The attributes that are mass assignable.
     *          
     * @var array
     */
    protected $fillable = [
        'comment_author_id',
        "comment_item_id",
        "comment_reply_id",
        "comment_content",
        "comment_updated_at",
        "comment_created_at"
    ];

   public $timestamps= false;
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    // protected $hidden = ["id"];

    public static function get_comments($item_id, $last_update)
    {
        //sacamos lo que hay en caché
        // $in_cache = Cache::store('file')->get('academy-courses');
        $in_cache = null;

        if ($in_cache === null) {
            //sacamos lo que hay en la db 
            $in_db = self::select(
                "comments.id",
                "comments.comment_author_id",
                "comments.comment_item_id",
                "comments.comment_reply_id",
                "comments.comment_content",
                "comments.comment_updated_at",
                "comments.comment_created_at",
                "user_datas.name",
                "user_datas.pic_url",
                "user_datas.flag",

                )
                ->leftjoin("user_datas", 'comments.comment_author_id', '=', 'user_datas.user_id')
                ->where("comments.comment_item_id", $item_id)
                ->where("comments.comment_updated_at", ">", $last_update)
                ->get();
            // //guardamos en caché
            // $expiresAt = Date::now()->addMinutes(env('CACHE_LIFE_TIME_ACADEMY', 1));
            // Cache::put('academy-courses', $in_db->toJson(), $expiresAt);

            // dd($in_db->toArray());
            return $in_db->toArray();
        }
        return json_decode($in_cache);
    }
}
