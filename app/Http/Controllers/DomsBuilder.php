<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Util\Json;

class DomsBuilder extends Controller
{

    public function uploadDom(Request $request, $domJsonName)
    {
        $user =  $request->user();
        $contents = json_decode(Storage::get("public/platform/" . $domJsonName . ".dom.json"));

        if ($contents->author_id === $user["id"]) {

            //hacemos una copia de seguridad
            $post_fix_name = "public/platform/" . $domJsonName;

            if (Storage::exists($post_fix_name . ".backup")) {
                Storage::delete($post_fix_name . ".backup");
            }
            Storage::copy($post_fix_name . ".dom.json", $post_fix_name . ".backup");

            //guardamos el dom
            Storage::delete($post_fix_name . ".dom.json");
            $request->file("blob")->storeAs("public/platform/", $domJsonName . ".dom.json");

            response("ok", 400);
        } else {
            response("not-permited", 400);
        }
    }

    public function getStorageMap(Request $request)
    {
        $user =  $request->user();
        $user_name = $user["user_name"];
        $files_for_all = Storage::files("public/platform/founders");
        $files = Storage::files("public/platform/" . $user_name);

        $all_files = array_merge($files_for_all, $files);
        $filter_files = [];

        foreach ($all_files as $key => $file) {
            if (strpos($file, ".thumbnail") === false)
                array_push($filter_files, str_replace("public/", "", $file));
        }

        $size = 0;
        foreach ($files as $key => $file) {
            $size = $size + Storage::size($file);
        }

        $data = [
            "files" => $filter_files,
            "size" => $size
        ];
        return response()->json($data, 200);
    }

    public function uploadToStorage(Request $request)
    {
        $user =  $request->user();
        $data = $request->all();

        // dd($request->file('thumbnail'));
        $request->file('blob')->storeAs("public/platform/" . $user["user_name"], $data["name"]);
        if ($request->file('thumbnail') !== null)
            $request->file('thumbnail')->storeAs("public/platform/" . $user["user_name"], $data["name"] . ".thumbnail");

        return response("platform/" . $user["user_name"] . "/" . $data["name"], 200);
    }
}
