<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'user_name', 'parent_id', 'email', 'password', "api_token"
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function user_data()
    {
        return $this->hasOne('App\User_data');
    }

    /*!
  =========================================================
  * 
  =========================================================
  */

    public static function get_user($email)
    {
        //sacamos lo que hay en caché
        Cache::delete('user_' . $email);
        $in_cache = Cache::get('user_' . $email);

        if ($in_cache === null) {
            //sacamos lo que hay en la db 
            $in_db = self::where("email", $email)->first();

            if ($in_db !== null) {
                //guardamos en cache
                $expiresAt = Date::now()->addMinutes(env('CACHE_LIFE_TIME_USER_DATA', 1));

                Cache::put('user_' . $email, json_encode($in_db->toArray()), $expiresAt);
                return array_merge($in_db->toArray(), ["from_chache" => false]);
            }
            return ["from_chache" => false];
        }
        return array_merge(json_decode($in_cache), ["from_chache" => true]);
    }

    /*!
  =========================================================
  * 
  =========================================================
  */

  public static function get_users($user_id)
  {
      //sacamos lo que hay en caché
      Cache::delete('users_' . $user_id);
      $in_cache = Cache::get('users_' . $user_id);

      if ($in_cache === null) {
          //sacamos lo que hay en la db 
          $in_db = self::where("parent_id", $user_id)->get();

          if ($in_db !== null) {
              //guardamos en la db
              $expiresAt = Date::now()->addMinutes(env('CACHE_LIFE_TIME_PAY_REPORTS', 5));
              Cache::put('users_' . $user_id, json_encode($in_db->toArray()), $expiresAt);

              return ["data" => $in_db->toArray(), "from_cache" => false];
          }
          return ["data" => [], "from_cache" => false];
      }
      return ["data" => json_decode($in_cache), "from_cache" => true];
  }

    /*!
  =========================================================
  * 
  =========================================================
  */

    public static function deliver_product($pay_report)
    {
        $id = $pay_report["user_id"];
        $product = $pay_report["product"];
        $parent_id = $pay_report["parent_id"];

        $users = self::get_users($parent_id)["data"];
        $user_key = array_search($id, array_column($users, "id"));

        if ($user_key !== false) {

            $user = $users[$user_key];
            $products = $user["products"] !== null ? json_decode($user["products"]) : [];
            $new_product = ["n" => $product];

            array_push($products, $new_product);
            
            //borramos el reporte de pago
            Pay_report::delete_report($pay_report);
            
            //cambiamos la base de datos (anadimos el producto)
            return self::where('id', $id)->update(["products" => json_encode($products)]);
        }
    }
}
