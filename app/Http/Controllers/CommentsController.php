<?php

namespace App\Http\Controllers;

use App\Comments;
use Illuminate\Http\Request;

class CommentsController extends Controller
{
    public function get_comments(Request $request)
    {
        $item_id = $request->get("item_id");
        $last_update = $request->get("last_update");

        $result = Comments::get_comments($item_id, $last_update);
        return response()->json(["comments"=>$result], 200, []);
    }

    public function add_comment(Request $request)
    {
        $data = $request->json()->all();
        $user =  $request->user();
        $unix = time();
        
        $inserted_data = array(
            'comment_author_id' => $user["id"],
            'comment_item_id' => $data["comment_item_id"],
            'comment_reply_id' => $data["comment_reply_id"],
            "comment_content" => $data["comment_content"],
            "comment_updated_at" =>  $unix,
            "comment_created_at" =>  $unix
        );

        $result = Comments::create($inserted_data)->toArray();
        return response()->json($result, 200, []);
    }
}
