<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pay_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_id',8)->unique();;
            $table->foreignId('user_id');
            $table->foreignId('parent_id');
            $table->string('product');
            $table->string('description');
            $table->integer('price')->nullable();
            $table->integer('img_number');

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
        Schema::dropIfExists('pay_reports');
    }
}
