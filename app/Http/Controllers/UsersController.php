<?php

namespace App\Http\Controllers;

use App\Events\UserDataChanged;
use App\User;
use App\User_data;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersController extends Controller
{

    /**
     * Create a new user
     *
     * @return void
     */
    public function register(Request $request)
    {
        if ($request->isjson()) {
            $messages = [
                'required' => "error-param",
                "email.unique" => "error-already-exist-email",
                "user_name.unique" => "error-already-exist-username",
            ];

            $this->validate($request, [
                'parent_user_name' => "required",
                'user_name' => "required|unique:users",
                'email' => 'required|email|unique:users',
                'password' => 'required'
            ], $messages);

            $data = $request->json()->all();

            //definimos el padre de este usuario
            $parent_data = User::where("user_name", $data["parent_user_name"])->first();
            $parent_id = 1;

            if ($parent_data !== null) {
                $parent_id = $parent_data->toArray()["id"];
            }

            //creamos token de session
            $tem_api_token = Str::random(60);

            //guardamos el usuario en la base de datos
            $inner_data = [
                // "api_token" => $tem_api_token,
                "parent_id" => $parent_id,
                "user_name" => $data["user_name"],
                "email" => $data["email"],
                "password" => Hash::make($data["password"]),
            ];
            $user = User::create($inner_data);

            //aumentamos el contador de usuarios del patrocinador
            $parent_data = User_data::get_data($parent_id)["data"];
            User_data::push_data($parent_id, ["users_counter" => ++$parent_data["users_counter"]]);

            //guardamos en caché la session del nuevo usuario
            $expiresAt = Date::now()->addMinutes(env('SESSION_LIFE_TIME', 10));
            Cache::put($tem_api_token, json_encode($user), $expiresAt);

            unset($user->id);
            return response()->json($user, 201);
        } else {
            return response()->json(["error" => "unAutorised!!"], 400, []);
        }
    }

    /*
  =========================================================
  * 
  =========================================================
  */

    public function login(Request $request)
    {
        $messages = [
            'required' => "error-param",
            "exists" => "error-unexist-email",
            '' => 'error-password'
        ];

        $this->validate($request, [
            'email' => 'required|email|exists:users',
            'password' => 'required',
            'remember_token' => 'required'
        ], $messages);

        if ($request->isJson()) {

            $data = $request->json()->all();
            $user = User::get_user($data["email"]);

            // dd($user);/

            if ($user && Hash::check($data["password"], $user["password"])) {

                $new_api_token = Str::random(60);

                if ($data["remember_token"]) {
                    Cache::forever($new_api_token, $data["email"]);
                } else {
                    $expiresAt = Date::now()->addMinutes(env('SESSION_LIFE_TIME', 10));
                    Cache::put($new_api_token, $data["email"], $expiresAt);
                }

                return response()->json(["api_token" => $new_api_token], 200);
            } else {
                return response()->json(["error" => "no content"], 406);
            }
        } else {
            return response()->json(["error" => "unAutorised!!"], 400, []);
        }

        // event(new UserDataChanged("eventoooooo"));
    }

    /*
  =========================================================
  * 
  =========================================================
  */

    public function logout(Request $request)
    {
        Cache::delete($request->header("api_token"));
        return response('', 200);
    }


    public function get_profile(Request $request, $id)
    {
        $user_data = User_data::get_data($id);
        return response()->json($user_data, 201);
    }
}
