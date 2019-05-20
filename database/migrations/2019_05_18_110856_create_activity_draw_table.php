<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityDrawTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_draw', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index()->comment('用户id');
            $table->unsignedTinyInteger('type')->comment('1:5元，2:10元，3:20，4:50元，5：手机');
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
        Schema::dropIfExists('activity_draw');
    }
}
