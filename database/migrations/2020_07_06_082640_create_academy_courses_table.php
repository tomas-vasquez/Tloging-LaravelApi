<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcademyCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('academy_courses', function (Blueprint $table) {
            $table->id();
            $table->string('course_title')->default("");
            $table->integer('course_author_id')->default(1);
            $table->string('course_pic_url')->default("");
            $table->string('course_desciption')->default("");
            $table->integer('course_level')->default(1);
            $table->integer('course_visits')->default(0);
            $table->string('course_state')->default("disaproved");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('academies');
    }
}
