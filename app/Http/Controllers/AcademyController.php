<?php

namespace App\Http\Controllers;

use App\AcademyCategories;
use App\AcademyCourses;
use App\AcademyItems;
use App\User_data;
use Illuminate\Http\Request;

class AcademyController extends Controller
{
    public function get_all (){
        $categories = AcademyCategories::all();
        $courses = AcademyCourses::all()->toArray();

        $authors_ids = [];
        foreach ($courses as $key => $course) {
            $author_id = $course["course_author_id"];
            if(!in_array( $author_id, $authors_ids)){
                array_push($authors_ids, $author_id);
            }
        }

        $authors = [];
        foreach ($authors_ids as $key => $author_id) {
            $author = User_data::get_data($author_id)["data"];
              array_push($authors, $author);
        }

        // dd($authors);

        return response()->json([
            "categories"=>$categories,
            "courses"=>$courses,
            "authors"=>$authors
        ], 200);
    }

    public function get_items (Request $request, $course_name){
        
        $items = AcademyItems::get_items($course_name);

        return response()->json([
            "items"=>$items,
        ], 200);
    }


    


}
