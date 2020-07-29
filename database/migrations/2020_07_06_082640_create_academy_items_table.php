<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcademyITemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('academy_items', function (Blueprint $table) {
            $table->id();
            
            $table->string('item_id')->unique();
            $table->string('item_title')->default("");
            $table->string('item_author_id')->default("");
            $table->string('item_content_url')->default("");
            $table->string('item_sort')->unique()->default("");
            $table->string('item_desciption')->default("");
            $table->integer('item_visits')->default(0);
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
