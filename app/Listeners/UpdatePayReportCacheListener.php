<?php

namespace App\Listeners;

use App\ChromePhp;
use App\Events\PayReportEvent;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;

class UpdatePayReportCacheListener
{

    public static function handle(PayReportEvent $event)
    {
        $event_tipe = $event->type;
        $pay_report =  $event->pay_report;

        switch ($event_tipe) {
            case 'deleted':
                self::update_cache_before_deleted($pay_report);
                break;

            case 'added':
                self::update_cache_before_add($pay_report);
                break;

            default:
                ChromePhp::error("updateCache: " . $$event_tipe . ": not implement");
                break;
        }
    }





    private static function update_cache_before_deleted($pay_report)
    {
        //actualizamos pay_reports_{(user_id)}

        $pay_report = (array)$pay_report;
        $user_id = $pay_report["user_id"];
        $report_id = $pay_report["report_id"];

        $in_cache = Cache::get('pay_reports_' . $user_id);
        
        if ($in_cache !== null) {

            $new_cache = array_filter(json_decode($in_cache), function ($pay_report) use ($report_id) {
                return $pay_report->report_id !== $report_id;
            });
            $expiresAt = Date::now()->addMinutes(env('CACHE_LIFE_TIME_PAY_REPORTS', 5));
            Cache::put('pay_reports_' . $user_id, json_encode($new_cache), $expiresAt);

            ChromePhp::log("updateCache: pay_reports_" . $user_id . ":",  Cache::get('pay_reports_' . $user_id));
        
        } else {
            ChromePhp::log("updateCache: pay_reports_" . $user_id . ": null");
        }


        $user_id = $pay_report["parent_id"];
        $report_id = $pay_report["report_id"];

        $in_cache = Cache::get('pay_reports_' . $user_id);

        if ($in_cache !== null) {

            $new_cache = array_filter(json_decode($in_cache), function ($pay_report) use ($report_id) {
                return $pay_report->report_id !== $report_id;
            });
            $expiresAt = Date::now()->addMinutes(env('CACHE_LIFE_TIME_PAY_REPORTS', 5));
            Cache::put('pay_reports_' . $user_id, json_encode($new_cache), $expiresAt);

            ChromePhp::log("updateCache: pay_reports_" . $user_id . ":",  Cache::get('pay_reports_' . $user_id));
        
        } else {
            ChromePhp::log("updateCache: pay_reports_" . $user_id . ": null");
        }
    }






    private static function update_cache_before_add($pay_report)
    {
        $user_id = $pay_report["user_id"];
        $in_cache = Cache::get('pay_reports_' . $user_id);

        if ($in_cache !== null) {

            $new_cache = array_merge(json_decode($in_cache), [$pay_report]);

            $expiresAt = Date::now()->addMinutes(env('CACHE_LIFE_TIME_PAY_REPORTS', 5));
            Cache::put('pay_reports_' . $user_id, json_encode($new_cache), $expiresAt);


            ChromePhp::log("updateCache: pay_reports_" . $user_id . ":", Cache::get('pay_reports_' . $user_id));
        } else {


            ChromePhp::log("updateCache: pay_reports_" . $user_id . ": null");
        }

        //cache del parent

        $user_id = $pay_report["parent_id"];
        $in_cache = Cache::get('pay_reports_' . $user_id);

        if ($in_cache !== null) {

            $new_cache = array_merge(json_decode($in_cache), [$pay_report]);

            $expiresAt = Date::now()->addMinutes(env('CACHE_LIFE_TIME_PAY_REPORTS', 5));
            Cache::put('pay_reports_' . $user_id, json_encode($new_cache), $expiresAt);

            ChromePhp::log("updateCache: pay_reports_" . $user_id . ":", Cache::get('pay_reports_' . $user_id));
        
        } else {
            ChromePhp::log("updateCache: pay_reports_" . $user_id . ": null");
        }
    }
}
