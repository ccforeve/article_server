<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('被咨询用户');
            $table->unsignedInteger('submit_user_id')->comment('咨询用户');
            $table->unsignedTinyInteger('type')->comment('咨询类型');
            $table->string('name', 30)->comment('姓名');
            $table->unsignedTinyInteger('age')->comment('年龄');
            $table->string('gender')->comment('性别');
            $table->string('phone', 11)->comment('手机号');
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
        Schema::dropIfExists('messages');
    }
}
