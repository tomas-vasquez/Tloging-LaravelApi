<?php

namespace App\Http\Controllers;

use App\Comments;
use Illuminate\Http\Request;
use PHPUnit\Util\Json;

class AutoRefresher extends Controller
{
    public function  index(Request $request)
    {
        $pack = $request->get("pack");
        $payload = [];

        foreach (json_decode($pack) as $key => $item) {
            $aux = explode("?", $item);
            $name = $aux[0];

            $item_id = isset($aux[1]) ? $aux[1] : null;
            $last_update = isset($aux[2]) ? $aux[2] : 0;

            switch ($name) {
                case 'comments':
                    $payload["comments"]["comments"] = Comments::get_comments($item_id, $last_update);
                    $payload["comments"]["comment_item_id"] = $item_id;
                    $payload["comments"]["last_update"] = time();
                    break;

                default:
                    break;
            }

            // dd(json_decode($last_update));
        }
        return response()->json($payload, 200);
    }
}
