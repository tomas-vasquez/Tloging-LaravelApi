<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Academy;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group(['prefix' => 'v1'], function () use ($router) {

    $router->post('user/login', "UsersController@login");
    $router->post('user/register', "UsersController@register");

    $router->get('academy/getall', "AcademyController@get_all");
    $router->get('academy/get_items/{course_name}', "AcademyController@get_items");

    $router->get("auto_refresher","AutoRefresher@index");

    
 $router->get('comments', "CommentsController@get_comments");


    $router->group(["middleware" => ["auth"]], function () use ($router) {

       
        $router->post('comments', "CommentsController@add_comment");


        $router->get('user/logout', "UsersController@logout");

        $router->get('user_data/pack/{pack_name}', "UserDatasController@index");
        $router->post('user_data', "UserDatasController@set_user_data");
        $router->get('user_data/{id}', "UsersController@get_profile");
        $router->post('user_pic', "UserDatasController@upload_pic");
        $router->delete('user_pic', "UserDatasController@delete_pic");

        $router->post('pay_report', "PayReportsController@store");
        $router->delete('pay_report/{report_id}', "PayReportsController@remove");
        $router->post('pay_report/aprove/{report_id}', "PayReportsController@aprove_report");
        
$router->post('platform/uploadToStorage', "DomsBuilder@uploadToStorage");
        $router->post('platform/{domJsonName}', "DomsBuilder@uploadDom");
        $router->get('platform/getStorageMap', "DomsBuilder@getStorageMap");
        
    });
});

$router->get('cache_clear', function () use ($router) {
    Cache::flush();
    return "cache success";
});

$router->get('show/{name}', function ($name) use ($router) {
    return Cache::get($name);
});
