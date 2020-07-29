<?php

namespace App\Http\Controllers;

use App\Academy;
use App\Pay_report;
use App\User;
use App\User_data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserDatasController extends Controller
{

    public function index(Request $request, $pack_name)
    {
        if ($pack_name === "init_pack") {
            $user =  $request->user();

            $platform = null;
            $platform_url = "public/platform/names/" . $user["user_name"] . ".json";
            $exists_platform = Storage::exists($platform_url);
            if ($exists_platform) {
                $platform = Storage::get($platform_url);
            } else {
                $platform = Storage::get("public/platform/names/founders.json");
            }

            $userData = User_data::get_data($user["id"]);
            $parent_data = User_data::get_data($user["parent_id"]);
            $pay_report = Pay_report::get_reports($user["id"]);
            $users = User::get_users($user["id"]);
            // $academy = Academy::get_all();/

            $data = [
                "user_id" => $user["id"],
                "user_data" => array_merge($user, $userData["data"]),
                "pay_reports" => $pay_report["data"],
                "parent_data" => $parent_data["data"],
                "users" => $users["data"],
                // "academy" => $academy,
                // "platform" =>  json_decode($platform),

                "cache" => [
                    "pay_reports" => $pay_report["from_cache"],
                    "userData" => $userData["from_cache"],
                    "parent_data" => $parent_data["from_cache"],
                    "users" => $users["from_cache"],
                    "platform" => false,
                    "platform" => false
                ]
            ];
            return response()->json($data, 200);
        }
    }

    /*!
  =========================================================
  * 
  =========================================================
  */

    public function set_user_data(Request $request)
    {
        $data = $request->json()->all();
        $user =  $request->user();
        $user_id = $user["id"];
        $new_data = User_data::push_data($user_id, $data);
        return response()->json($new_data, 201);
    }

    /*!
  =========================================================
  * 
  =========================================================
  */

    public function upload_pic(Request $request)
    {
        $user = $request->user();

        // return json_encode($user);
        //borramos la imagen antigua
        $user_data = User_data::get_data($user["id"])["data"];

        if (isset($user_data["pic_url"])) {
            Storage::delete('public/' . $user_data["pic_url"]);
        }

        //guardamos la imagen que nos llega
        $pic_name = $user["user_name"] . "-" . time() . ".jpg";
        $request->file('blob')->storeAs("public/pics", $pic_name);

        //cambiamos la url en la base de datos
        $new_pic_url = "pics/" . $pic_name;

        User_data::push_data($user["id"], ["pic_url" => $new_pic_url]);

        return $new_pic_url;
    }

    /*!
  =========================================================
  * 
  =========================================================
  */

    public function delete_pic(Request $request)
    {
        $user =  $request->user();
        $pic_url = User_data::get_data($user["id"])["data"]["pic_url"];

        Storage::delete('public/' . $pic_url);
        User_data::push_data($user["id"], ["pic_url" => null]);
        return response('', 200);
    }
}
