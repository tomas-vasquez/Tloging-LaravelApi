<?php

namespace App;

use App\Events\PayReportDeleted;
use App\Events\PayReportEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;

class Pay_report extends Model
{
    /**
     * The attributes that are mass assignable.
     *          
     * @var array
     */
    protected $fillable = [
        "report_id",
        "user_id",
        "parent_id",
        "img_number",
        "description",
        "product",
    ];
    /*!=========================================================*/

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ["id"];


    public static function get_reports($user_id)
    {
        //sacamos lo que hay en caché
        //Cache::delete('pay_reports_' . $user_id);
        $in_cache = Cache::get('pay_reports_' . $user_id);

        if ($in_cache === null) {
            //sacamos lo que hay en la db 
            $in_db = self::where("user_id", $user_id)->orWhere("parent_id", $user_id)->get();

            if ($in_db !== null) {
                //guardamos en la cache
                $expiresAt = Date::now()->addMinutes(env('CACHE_LIFE_TIME_PAY_REPORTS', 5));
                Cache::put('pay_reports_' . $user_id, json_encode($in_db->toArray()), $expiresAt);

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

    public static function push_report($inserted_data)
    {
        //actualizamos la db
        $result = self::create($inserted_data);

        event(new PayReportEvent("added", $inserted_data));

        return $result;
    }

    /*!
  =========================================================
  * 
  =========================================================
  */

    public static function delete_report($pay_report)
    {
        ChromePhp::log($pay_report);
        $pay_report = (array) $pay_report;
        $report_id = $pay_report["report_id"];

        //borramos las imagenes
        $pic_url = "/report-" . $report_id . "-0.jpg";
        if (Storage::exists('public/pay_reports' . $pic_url)) {
            Storage::delete('public/pay_reports' . $pic_url);
        }
        $pic_url = "/report-" . $report_id . "-1.jpg";
        if (Storage::exists('public/pay_reports' . $pic_url)) {
            Storage::delete('public/pay_reports' . $pic_url);
        }
        $pic_url = "/report-" . $report_id . "-2.jpg";
        if (Storage::exists('public/pay_reports' . $pic_url)) {
            Storage::delete('public/pay_reports' . $pic_url);
        }

        //actualizamos la db
        $result = self::where('report_id', $report_id)->delete();

        event(new PayReportEvent("deleted", $pay_report));

        return $result;
    }
}
