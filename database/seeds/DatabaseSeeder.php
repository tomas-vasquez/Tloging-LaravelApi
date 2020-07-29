<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("users")->insert([
            "id" => 1,
            "user_name" => "tomasdetloging",
            "parent_id" => 1,
            "email" => "tomasdetloging@gmail.com",
            "created_at" => Date::now(),
            "password" => '$2y$10$PcHDkSeNHzrEvaPGGq0xxO18g/qk6dkG1c6jeD5h7CuFj5WaJTpA2'
        ]);
        DB::table("user_datas")->insert([
            "user_id" => 1,
            "users_counter" => 2344,
            "name" => "Tomas Vasquez",
            "pic_url" => "pics/founders.jpg",
            "flag" => "BO",
        ]);

        // for ($i=2; $i < 20; $i++) { 
        //     DB::table("users")->insert([
        //         // "id" => $i,
        //         "user_name" => "fake_user".$i,
        //         "parent_id" => 1,
        //         "email" => "fake".$i."@tloging.com",
        //         "password" => '$2y$10$PcHDkSeNHzrEvaPGGq0xxO18g/qk6dkG1c6jeD5h7CuFj5WaJTpA2'
        //     ]);
        // }

        DB::table("academy_categories")->insert([
            'category_id' => '1',
            'category_title' => 'Programacion web',
            'category_pic_url' => '/ca2f7ab7-5458-48d7-aab3-0eaa7d20abc4.png',
            'category_desciption' => 'descripcion de estaa vaina',
            'category_visits' => 45
        ]);

        DB::table("academy_categories")->insert([
            'category_id' => '2',
            'category_title' => 'Programacion web 22',
            'category_pic_url' => '/ca2f7ab7-5458-48d7-aab3-0eaa7d20abc4.png',
            'category_desciption' => 'descripcion22222',
            'category_visits' => 45
        ]);
        /////////////
        DB::table("academy_courses")->insert([
            'course_id' => '1',
            'course_title' => 'Curso de programacion basica',
            'course_pic_url' => '/ca2f7ab7-5458-48d7-aab3-0eaa7d20abc4.png',
            'course_desciption' => 'descripcion22222',
            'course_visits' => 45,
            'course_state' => "aproved"
        ]);

        DB::table("academy_courses")->insert([
            'course_id' => '2',
            'course_title' => 'Curso de wordpress',
            'course_pic_url' => '/82dfe34a-4e3b-4904-bdb5-03f45112fcd2.png',
            'course_desciption' => 'descripcion22222',
            'course_visits' => 45,
            'course_state' => "aproved"
        ]);

        //////////////////////////////

        DB::table("academy_items")->insert([
            'item_id' => '1',
            'item_title' => 'introduccion',
            'item_author_id' => 1,
            'item_content_url' => "123.json",
            'item_visits' => 4523,
            'item_sort' => 1,
            'item_desciption' => "conceptos basicos",
        ]);

        DB::table("academy_items")->insert([
            'item_id' => '2',
            'item_title' => 'que es una variable',
            'item_author_id' => 1,
            'item_content_url' => "123.json",
            'item_visits' => 223,
            'item_sort' => 2,
            'item_desciption' => "explicacion de que es una variable",
        ]);
    }
}
