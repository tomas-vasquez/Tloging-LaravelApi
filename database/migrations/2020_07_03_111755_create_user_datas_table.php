<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_datas', function (Blueprint $table) {

            $table->foreignId('user_id')->references('id')->on("users")->onDelete("cascade");

            $table->integer('users_counter')->default(0);

            $table->string('name')->nullable();
            $table->string('pic_url')->nullable();
            $table->string('flag', 4)->nullable();
            $table->string('area_code', 5)->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('link_facebook')->nullable();
            $table->string('link_instagram')->nullable();
            $table->string('link_twitter')->nullable();
            $table->string('description')->nullable();
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
        Schema::dropIfExists("user_datas");
    }
}